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
                            <strong>TeleSales</strong>
                        </li>
                        <li class="active">
                            <strong>Tele Order</strong>
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
                        <div class="x_title">
                            <center>
                                <btn class="btn btn-success" id="non-productive-order" onclick="nonProductiveOrder()"
                                     style="color: #283a97;background: transparent;">
                                    ::: Order :::
                                </btn>
                                <btn class="btn btn-info" id="edit-order"
                                     onclick="teleOrderHistory()" style="color: #283a97;background: transparent;"> ::: Order Edit :::
                                </btn>
                            </center>
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <div class="x_panel">

                        <div class="x_content">
                            <form class="form-horizontal form-label-left"
                                  action="{{url ('tele-order-promotion-store')}}" id="tele-order"
                                  method="post" enctype="multipart/form-data" autocomplete="off">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}

                                <div class="row" >

                                    <input type="hidden" id="con"/>
                                    <input type="hidden" id="country_id"/>
                                    <input type="hidden" id="employee-id"/>
                                    <input type="hidden" id="sales-group-id"/>
                                    <input type="hidden" id="promotion-zone-id"/>
                                    <input type="hidden" id="invoice-special-discount-percent"/>
                                    <input type="hidden" id="invoice-special-discount-amount"/>
                                    <input type="hidden" id="item-special-discount"/>
                                    <input type="hidden" id="global-special-discount-amount"/>
                                    <input type="hidden" name="has_special_discount" id="has-special-discount"/>
                                    <input type="hidden" id="outlet-id" name="outlet_id"/>
                                    <input type="hidden" id="tracking-id" name="tracking_id"/>
                                    <input type="hidden" id="non-productive" value="0"/>
                                    <input type="hidden" id="np-dlrm-id"/>
                                    <input type="hidden" id="grv-id" name="grv_id"/>
                                    <input type="hidden" value="0" id="grv-exist" name="grv_exist"/>

                                    <div class="col-md-12 col-sm-12 shadow-div">
                                        <ul class="nav" style="display: flex;
                                                        gap: 1rem;
                                                        padding-left: 0px;
                                                        justify-content: space-between;">
                                            <li class="order-sidebar">
                                                <select class="form-control cmn_select2"
                                                        name="country_id" required="required" onchange="getCompanies();getNoteNonProductiveTypes()"
                                                        id="country-id">
                                                    <option value="">Select Country</option>
                                                    @foreach($countries as $country)
                                                        <option value="{{$country->id}}">{{$country->cont_name}}</option>
                                                    @endforeach
                                                </select>
                                            </li>
                                            <li class="order-sidebar">

                                                <select class="form-control cmn_select2"
                                                        name="acmp_id" required="required"
                                                        id="acmp-id" onchange="getGroups()">

                                                    <option value="">Select BU</option>
                                                </select>
                                            </li>
                                            <li class="order-sidebar">

                                                <select class="form-control cmn_select2"
                                                        name="slgp_id" required="required" onchange="storeSalesGroup()"
                                                        id="slgp-id">

                                                    <option value="">Select Group</option>
                                                </select>
                                            </li>
                                            <li class="order-sidebar">
                                                <select class="form-control cmn_select2"
                                                        name="zone_id" required="required"
                                                        id="zone-id"  onchange="getSrs()">

                                                    <option value="">Select Zone</option>
                                                </select>
                                            </li>
                                            <li class="order-sidebar">
                                                <select class="form-control cmn_select2"
                                                        name="sr_id" required="required"
                                                        id="sr-id" onchange="getRoutes();getDistributors()">

                                                    <option value="">Select SR</option>
                                                </select>
                                            </li>
                                            <li class="order-sidebar">
                                                <select class="form-control cmn_select2"
                                                        name="dlrm_id" required="required"
                                                        id="dlrm-id">

                                                    <option value="">Select Distributor</option>
                                                </select>
                                            </li>
                                            <li class="order-sidebar">
                                                <select class="form-control cmn_select2"
                                                        name="rout_id" required="required"
                                                        id="rout-id" onchange="getOutlets()">

                                                    <option value="">Select Rout</option>
                                                </select>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="col-md-5 col-sm-5" style="padding-left: 0; height: 62vh!important;">
                                        <div class="col-md-12 col-sm-12 shadow-div"  style="height: 62vh!important;">

                                            <div id="sales_heirarchy" class="col-md-12 " style="height: 135px!important;">
                                                <div class="item form-group">
                                                    <label class=" col-md-2 col-sm-2 col-xs-12" for="outlet-name">Name:
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <input type="text" class="outlet-info in_tg" name="outlet_name" id="outlet-name" placeholder="Outlet"
                                                               disabled/>
                                                    </div>
                                                    <label class=" col-md-2 col-sm-2 col-xs-12" for="outlet-code">Code:
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <input type="text" class="outlet-info in_tg" name="outlet_code" id="outlet-code" placeholder="Code"
                                                               disabled/>
                                                    </div>
                                                </div>
                                                <div class="item form-group">
                                                    <label class=" col-md-2 col-sm-2 col-xs-12" for="outlet-district">District:
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <input type="text" class="outlet-info in_tg" name="outlet_district" id="outlet-district" placeholder="District"
                                                               disabled/>
                                                    </div>
                                                    <label class=" col-md-2 col-sm-2 col-xs-12" for="outlet-thana">Thana:
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <input type="text" class="outlet-info in_tg" name="outlet_thana" id="outlet-thana" placeholder="Thana"
                                                               disabled/>
                                                    </div>
                                                </div>
                                                <div class="item form-group">
                                                    <label class=" col-md-2 col-sm-2 col-xs-12" for="outlet-mobile">Mobile:
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <input type="text" class="outlet-info in_tg" name="outlet_mobile" id="outlet-mobile" placeholder="Mobile"
                                                               disabled/>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <i class="fa fa-clipboard fa-lg" onclick="mobileToClipboard()" style="color: green" aria-hidden="true"></i>
                                                        <span id="copy-message" style="display: none; margin-left: 0.45rem;" class="ml-2 text-success">Copied</span>
                                                        <a target="_blank" id="whats-app"
                                                           style="margin-left: 0.65rem;"
                                                           class="request_report_check ml-3">
                                                            <img style="width: 2.65rem;" src="{{asset('/theme/image/whatsapp.png')}}">
                                                        </a>
                                                        <a target="_blank" id="skype-app"
                                                           style="margin-left: 0.65rem;"
                                                           class="request_report_check ml-3">
                                                            <img style="width: 2.65rem;" src="{{asset('/theme/image/skype.png')}}">
                                                        </a>
                                                    </div>
                                                </div>

                                                <div class="item form-group">
                                                    <label class=" col-md-2 col-sm-2 col-xs-12" for="outlet-address">Address:
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <input type="text" class="outlet-address in_tg" name="outlet_address" id="outlet-address" placeholder="Address"
                                                               disabled/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="order-history" class="col-md-12 " style="height: 21vh; padding-right: 0; overflow-y: scroll; display: none;">
                                                <table class="table" id="order-table"
                                                       style="padding-top: 10px; padding-left: 5px; margin: 0">
                                                    <thead>
                                                    <tr class="tbl_header_light">
                                                        <th>SL</th>
                                                        <th>DATE</th>
                                                        <th>ORDER NUM</th>
                                                        <th>STATUS</th>
                                                        <th>AMNT</th>
                                                        <th>ACTION</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="order-list">
                                                    </tbody>
                                                </table>

                                            </div>

                                            <div id="msp-block" class="col-md-12 " style="height: 20.55vh; margin-top: 5px; padding-right: 0; overflow-y: scroll; display: none;">
                                                <table class="table" id="msp-table"
                                                       style="padding-top: 10px; padding-left: 5px; margin: 0">
                                                    <thead>
                                                    <tr class="tbl_header_light">
                                                        <th>CODE</th>
                                                        <th>NAME</th>
                                                        <th>MSP QTY</th>
                                                        <th>ORD QTY</th>
                                                        <th>DELI QTY</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="msp-history">
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="col-md-8 col-sm-8" style="position: absolute; right: 10px; bottom: 5px;">
                                            <div class="col-md-8 col-sm-8">
                                                <span id="outlet-serial"></span>
                                            </div>
                                            <button id="next" type="button" onclick="storeTeleOrder()" class="btn btn-xs btn-info"
                                                    style=" float: right;">Next
                                            </button>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-7 col-sm-7 shadow-div" style="text-align: center; height: 62vh!important;">
                                        <div id="order-options" style="display: flex; justify-content: center;">

                                            <div id="order-selection-options" style="display: flex; justify-content: center; align-items: center" >

                                                <label for="has-note" style="margin-right: 5px; margin-left: 5px">
                                                    <input type='radio' id="has-note" name='note' value="1" onclick="showNote()" > Note
                                                </label>

                                                <label id="non-productive-option" for="non_productive">
                                                    <input type='radio' onclick="isNonProductive()" id="non_productive" value="0"
                                                           name='non_p_or_order'> <span style="margin-left: .01rem;"> Non Productive </span>
                                                </label>

                                                <label for="order" style=" margin-left: 5px">
                                                    <input type='radio' value="1" name='non_p_or_order' onclick="isOrder()" id="order"><span style="margin-left: .01rem;">
                                                        Order</span>
                                                </label>

                                                <label class="control-label" for="recording-file" style="text-align: center;padding-left:15px">
                                                    Upload Audio<span
                                                            class="required">*</span>
                                                    <input id="recording-file" class="form-control in_tg" required
                                                           name="recording_file" placeholder="recording file" type="file">
                                                </label>
                                            </div>

                                            <div id="np-outlet-selection" class="col-md-6" style="display: none">
                                                <select class="form-control cmn_select2"
                                                        name="nop_outlet_id" id="nop-outlet-id" onchange="loadNopOutletInfo()">
                                                    <option value="">Select Outlet</option>
                                                </select>
                                            </div>

                                            <div id="outlet-selection" class="col-md-6" style="display: none">
                                                <select class="form-control cmn_select2"
                                                        name="order_outlet_id" id="order-outlet-id" onchange="loadOutletInfo()">
                                                    <option value="">Select Outlet</option>
                                                </select>
                                            </div>

                                            <div id="tele-order-outlet-search" class="col-md-12" style="display: none">
                                                <div id="ordered-outlets-section" class="col-md-6">
                                                    <select class="form-control cmn_select2 in_tg"
                                                            id="order-outlets" onchange="loadOrderHistory()">
                                                        <option value="">Select Outlet</option>
                                                    </select>
                                                </div>
                                                <div id="order-history-section" class="col-md-6">
                                                    <select class="form-control cmn_select2 in_tg"
                                                            id="order-history-selection" onchange="loadOrderInfo()">
                                                        <option value="">Select Outlet First !!!</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-sm-12 col-xs-12 note-field" style="display: none">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="note-type" style="text-align: left;padding-left: 0 !important;">
                                                Note Type<span class="required">*</span></label>
                                                <select class="form-control cmn_select2" name="ntpe_id" id="note-type">
                                                    <option value="">Select a Country First</option>
                                                </select>
                                            </div>
                                            <label class="control-label col-md-6 col-sm-6 col-xs-12 text_left"
                                                   for="note" style="text-align: left">Note<span
                                                        class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <textarea class="form-control in_tg" name="order_note" id="note" autocomplete="off"></textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-sm-12 col-xs-12 non-productive-field" style="display: none; margin-bottom: .75rem">
                                            <input type="hidden" name="npro_note" id="npro-note"/>

                                            <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                                   for="non-productive-type" style="text-align: left">Type<span id="note-type-span"
                                                        class="required">*</span></label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">

                                                <select class="form-control cmn_select2" name="nopr_id" onchange="setNonProductiveNote()" id="non-productive-type">
                                                    <option value="">Select a Country First</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-sm-12 col-xs-12 order-file"
                                             style="display: none; margin-top: .75rem; text-align: center">

                                            <input type="hidden" name="plmt_id" id="plmt-id"/>

                                            <div class="col-md-6 col-sm-6 col-xs-6" style=" padding: 0">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                                       for="grv-cat-id" style="text-align: left; padding: 0">Category<span
                                                            class="required">*</span></label>
                                                <div class="col-md-12 col-sm-12 col-xs-12" style=" padding: 0">

                                                    <select class="form-control cmn_select2" onchange="getItems()" id="cat-id" required>
                                                        <option value="">Select a Category</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-6" style="padding: 0; padding-left: 5px">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                                       for="amim-id" style="text-align: left; padding: 0">Item<span
                                                            class="required">*</span></label>
                                                <div class="col-md-12 col-sm-12 col-xs-12" onchange="getOrders()" style=" padding: 0">

                                                    <select class="form-control cmn_select2" id="amim-id" required>
                                                        <option value="" disabled>Select an Item</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top: .75rem; padding: 0;text-align: left">

                                                    <h4 class="text-center" style="margin: 0">Items</h4>

                                                    <div id="items" style="overflow-y: scroll; height: 19.5vh">
                                                        <table class="table table-responsive table-borderless" style="border: none;overflow-y: scroll; max-height: 16vh !important; padding-top: 10px; padding-left: 5px; margin: 0">
                                                            <thead style="position: sticky; top: 0px;">
                                                            <tr class="tbl_header_light">
                                                                <th style="width: 55% !important; text-align: left">Item</th>
                                                                <th style="width: 10% !important;">Pcs</th>
                                                                <th style="width: 10% !important;">Ctn</th>
                                                                <th style="width: 10% !important;">Discount</th>
                                                                <th style="width: 10% !important;">Subtotal</th>
                                                                <th style="width: 5% !important;">Action</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="items-list">
                                                            </tbody>
                                                            <tfoot style=" position: sticky;
                                                                           bottom: -1px;
                                                                           z-index: 9;
                                                                           height: initial;
                                                                           background: white;">
                                                                <tr id="item-footer" style="">
                                                                    <td style=" display: flex;
                                                                                padding: 0;
                                                                                gap: 17px;
                                                                                align-items: center;
                                                                                text-align: start!important;">
                                                                            <span style=" width: 62%; text-align: end; color: blue"> Special Discount </span>

                                                                        <div class="input-group" style="display: flex;margin-bottom: 0px!important">
                                                                            <input type="number" style="width: 59px;text-align: end;padding-right: 18px !important;border: 1px solid blue !important"
                                                                                   class="in_tg" oninput="invoiceWiseSpecialPercentDiscount()" id="special-discount-percent">

                                                                            <div class="input-group-append" style=" position: absolute; bottom: 5px; right: 4px;">
                                                                                <span class="input-group-text"><i class="fa fa-percent"></i></span>
                                                                            </div>
                                                                        </div>
                                                                        <span style="width: 7%;">OR</span>

                                                                        <input type="number" style="width: 19%;text-align: end;padding: 0;height: 27px;border: 1px solid blue !important" class="in_tg" id="special-discount"
                                                                               oninput="invoiceWiseSpecialAmountDiscount()" name="special_discount">
                                                                    </td>
                                                                    <td colspan="2" style="padding: 0 13px;">
                                                                        <input class="btn btn-success" onclick="invoiceWiseSpecialDiscountCalculation()" readonly=""
                                                                               style=" height: 27px; margin-top: 8px; vertical-align: center; width: 59%;" value="Adjust">
                                                                    </td>
                                                                    <td style="vertical-align: middle;padding: 0 2px 0 0 !important">
                                                                        <input type="number" style="width: 100%; text-align: end; padding: 0;" readonly=""
                                                                               class="in_tg" id="total-discount">
                                                                    </td>
                                                                    <td style="padding: 0 1.5px;vertical-align: middle;">
                                                                        <input type="number" style="width: 100%; text-align: end; padding: 0" readonly="" class="in_tg" id="total-price" name="total">
                                                                    </td>
                                                                    <td>

                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>

                                                    <div id="grv-area" class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 1rem; padding: 0">
                                                        <div class="col-md-6 col-sm-6 col-xs-6" style=" padding: 0">
                                                            <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                                                   for="grv-cat-id" style="text-align: left; padding: 0">Grv Category<span
                                                                        class="required">*</span></label>
                                                            <div class="col-md-12 col-sm-12 col-xs-12" style=" padding: 0">

                                                                <select class="form-control cmn_select2" onchange="getGrvItems()" id="grv-cat-id" required>
                                                                    <option value="">Select Grv Category</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-sm-6 col-xs-6" style="padding: 0; padding-left: 5px">
                                                            <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                                                   for="grv-amim-id" style="text-align: left; padding: 0">Grv Item<span
                                                                        class="required">*</span></label>
                                                            <div class="col-md-12 col-sm-12 col-xs-12" onchange="getGrvOrders()" style=" padding: 0">

                                                                <select class="form-control cmn_select2" id="grv-amim-id" required>
                                                                    <option value="" disabled>Select Grv Item</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                    <h4 class="text-center" style="margin: 0">GRV Items</h4>

                                                    <div id="grv-items" style="overflow-y: scroll; height: 18.2vh">
                                                        <table class="table table-responsive table-borderless" style="border: none;overflow-y: scroll; max-height: 17vh; padding-top: 10px; padding-left: 5px; margin: 0">
                                                            <thead style="position: sticky; top: 0px;">
                                                            <tr class="tbl_header_light">
                                                                <th style="width: 55% !important; text-align: left">Item</th>
                                                                <th style="width: 10% !important;">Pcs</th>
                                                                <th style="width: 10% !important;">Ctn</th>
                                                                <th style="width: 10% !important;">Subtotal</th>
                                                                <th style="width: 5% !important;">Action</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="grv-items-list">
                                                            </tbody>
                                                            <tfoot style=" position: sticky;
                                                                                   bottom: -1px;
                                                                                   z-index: 9;
                                                                                   height: initial;
                                                                                   background: white;">
                                                            <tr id="grv-item-footer" style="display: none">
                                                                <td ></td>
                                                                <td colspan="2" style="vertical-align: middle; text-align: right">Grv Total</td>
                                                                <td style="padding: 1.5px;">
                                                                    <input type="number" style="width: 100%; text-align: end; padding: 0" readonly class="in_tg" id="grv-total-price">
                                                                </td>
                                                                <td>

                                                                </td>
                                                            </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="modal fade" id="showSpecialDiscount" role="dialog" style="z-index: 10;">
                                    <div class="modal-dialog modal-lg">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" onclick="resetSpecialDiscount()" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title text-center">Special Discount</h4>
                                            </div>
                                            <div class="modal-body" style="text-align: center;">
                                                <input type="hidden" id="special-discount-id">


                                                <div id="special-discount-fields" style="margin-bottom: 6px;">

                                                </div>

                                                <div class="text-center " style="display: flex; justify-content: center;">
                                                    <button class="btn btn-success col-md-3 col-sm-3"
                                                            type="button" id="foc-promotion-btn" onclick="specialDiscountCalculation()"> Add Special Discount
                                                    </button>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" onclick="resetSpecialDiscount()" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>


                                <div class="modal fade" id="showPromotion" role="dialog" style="z-index: 10;">
                                    <div class="modal-dialog modal-lg">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" onclick="resetPromotion()" class="close" data-dismiss="modal">&times;</button>
                                                <h4 id="promotion-heading" class="modal-title text-center">Choose Promotion Items</h4>
                                            </div>
                                            <div class="modal-body">
                                                <table class="table font_color text-center" data-page-length="50">
                                                    <thead id="slub-details">
                                                    <tr class="tbl_header_light">
                                                        <th class="text-center" style="width: 50% !important;">Order Qty</th>
                                                        <th class="text-center" style="width: 50% !important;">Free Qty</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="slub-info">

                                                    </tbody>
                                                </table>
                                                <input type="hidden" id="foc-promotion-id">
                                                <input type="hidden" id="promotion-slgp-id">
                                                <input type="hidden" id="order-qty">
                                                <input type="hidden" id="prom-type">

                                                <table class="table font_color" data-page-length="50">
                                                    <thead id="promotion-details">
                                                    <tr class="tbl_header_light">
                                                        <th style="width: 65% !important; text-align: left">Item</th>
                                                        <th style="width: 15% !important;">Pcs</th>
                                                        <th style="width: 15% !important;">Ctn</th>
                                                        <th style="width: 5% !important;">Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="promotion-info">


                                                    </tbody>
                                                </table>
                                                <div class="text-center " style="display: flex; justify-content: center;">
                                                    <button class="btn btn-success col-md-3 col-sm-3" style="width: 15%;" type="button" id="foc-promotion-btn" onclick="getPromotionCalculation()">
                                                        Promotion</button>
                                                    <button class="btn btn-info col-md-3 col-sm-3" style="width: 15%;"  type="button" id="foc-promotion-btn" onclick="insertFocIntoOrder()">
                                                        Add Items</button>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" onclick="resetPromotion()" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </form>
                        </div>


                    </div>


                    @if(!$has_access)
                        <script type="text/javascript">
                            Swal.fire({
                                icon: 'warning',
                                title: 'Please Give Attendance to Continue',
                                confirmButtonText: 'Attendance',
                                allowEscapeKey: false,
                                allowOutsideClick: false,
                                backdrop: 'static'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    let token = $('#_token').val();
                                    $.ajax({
                                        type: "POST",
                                        url: "{{URL::to('/')}}/give-attendance",
                                        data: {
                                            _token: token,
                                        },
                                        cache: "false",
                                        success: function (data) {
                                            $("select").select2();

                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Successful',
                                                text: "Attendance Given Successfully",
                                            });
                                        },
                                        error: function (data){
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Faild',
                                                text: "Invalid Information",
                                            });
                                        }
                                    })
                                }
                            })
                        </script>
                    @else
                        <script>
                            $("select").select2();
                        </script>
                    @endif
                </div>
        </div>
    </div>
    <style>
        .shadow-div{
            min-height: 50px;
            padding: 10px 0px 15px 0px;
            -webkit-box-shadow: 1px 1px 5px 2px rgb(0 0 0 / 21%);
            box-shadow: 1px 1px 5px 2px rgb(0 0 0 / 21%);
            margin-bottom: 15px;
        }

        input{
            height: 24px!important;
        }

        .item-subtotal, #total-price{
            border: 1px solid #5bf728 !important;
        }

        .table>tfoot>tr>td{
            border: 0!important;
        }

        #special-discount-percent::-webkit-outer-spin-button,
        #showSpecialDiscount [id^="special-discount-percent-"]::-webkit-outer-spin-button,
        #special-discount-percent::-webkit-inner-spin-button,
        #showSpecialDiscount [id^="special-discount-percent-"]::-webkit-inner-spin-button,
        #special-discount-percent::-webkit-slider-thumb ,
        #showSpecialDiscount [id^="special-discount-percent-"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            margin: 0;
        }

        #item{
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .in_tg{
            border: 1px solid #ccc !important;
            padding: 0.25rem !important;
        }

        .order-sidebar{
            width: 14.28%;
            margin: 0;
            margin-bottom: .75rem;
            height: 3.15rem;
        }

        .outlet-info{
            width: 100%;
        }

        .order-file-download:hover, .fa-clipboard:hover{
            cursor: pointer;
        }

        .table>thead>tr>th, .table>tbody>tr>td {
            padding: 2px !important;
            border: none;
        }

        .order-list-text{
            font-size: 1.21rem;
            line-height: .15rem !important;
        }

        .fa-times-circle-o:hover, .fa-eye:hover, #whats-app:hover, #skype-app:hover, #non-productive-order:hover, .fa-edit:hover{
            cursor: pointer;
        }

    </style>

    <script src="{{ asset("theme/vendors/js/aws-sdk-2.971.0.min.js")}}"></script>

    <script type="text/javascript">
/*
        AWS.config.update({
            region: '{{ env('AWS_REGION', 'us-east-1') }}',
            credentials: new AWS.Credentials({
                accessKeyId: 'NKHLZGVLLLAIV62USI5G',
                secretAccessKey: '+hznHV41sb/5vlStEUczr0FZlS57hYWnNZh4HY6SSgk'
            })
        });

        let s3 = new AWS.S3();

        let bucketName = 'prgfms';*/

        let token = $('#_token').val();


        let need_audio = true;
        let outlets = [];
        let total_outlet;

        let item_qtys = []
        let item_ctns = []
        let item_discounts = []
        let item_subtotals = []

        $(document).ready(function () {

            $('.order-file').hide()
            $("#showPromotion").hide();

            setTimeout(function () {
                $('#menu_toggle').click();
            }, 1);
        });

        // Special Discount Section
        function invoiceWiseSpecialPercentDiscount(){
            let item_wise_discount = $('#item-special-discount').val()

            if(item_wise_discount === '1') {
                Swal.fire({
                    icon: 'info',
                    title: 'Item wise discount already exist! do you want invoice wise discount',
                    showCancelButton: true,
                    confirmButtonText: 'Invoice wise discount',
                    cancelButtonText: 'Item wise discount',

                }).then((result) => {
                    if (result.isConfirmed) {
                        $('.special-discount').each(function () {
                            let item_id = $(this).attr('id').split('-')[2];
                            $(`#special-disc-info-${item_id}`).removeAttr("value")
                            $(this).val(0);
                        });

                        getDiscountTotal()

                        $('#item-special-discount').removeAttr("value")

                        let invoice_discount_percent = $('#special-discount-percent').val()

                        if (invoice_discount_percent > 100) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: "Value can't be more then 100%",
                            });

                            $(`#special-discount-percent`).val(0)

                            return;
                        }

                        $('.special-discount').each(function () {
                            $(this).val(0);
                        });

                        $('#item-special-discount').val(0)

                        $('#invoice-special-discount-percent').val(1)
                        $('#invoice-special-discount-amount').val(0)
                    }else{
                        return
                    }
                })
            }
            else{
                $('#invoice-special-discount-percent').val(1)
                $('#invoice-special-discount-amount').val(0)
            }

        }

        function invoiceWiseSpecialAmountDiscount(){
            let item_wise_discount = $('#item-special-discount').val()


            if(item_wise_discount === '1') {

                Swal.fire({
                    icon: 'info',
                    title: 'Item wise discount already exist! do you want invoice wise discount',
                    showCancelButton: true,
                    confirmButtonText: 'Invoice wise discount',
                    cancelButtonText: 'Item wise discount',

                }).then((result) => {
                    if (result.isConfirmed) {
                        let total = 0
                        $('.special-discount').each(function () {
                            let item_id = $(this).attr('id').split('-')[2];
                            $(`#special-disc-info-${item_id}`).removeAttr("value")
                            total += Number($(`#item-subtotal-${item_id}`).val())
                            $(this).val(0);
                        });

                        getDiscountTotal()

                        $('#item-special-discount').removeAttr("value")

                        let invoice_discount_amount = $('#special-discount').val()

                        if (invoice_discount_amount > total) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: "Value can't be more then subtotal",
                            });

                            $(`#special-discount-percent`).val(0)

                            return;
                        }

                        $('#item-special-discount').val(0)
                        $('#invoice-special-discount-amount').val(1)
                        $('#invoice-special-discount-percent').val(0)
                    }else{
                        return
                    }
                })
            }
            else {
                $('#invoice-special-discount-amount').val(1)
                $('#invoice-special-discount-percent').val(0)
            }
        }

        function invoiceWiseSpecialDiscountCalculation(){
            let item_id
            let global_discount

            let total_price = 0

            $('.special-discount').each(function () {
                item_id = $(this).attr('id').split('-')[2];
                total_price += Number($(`#item-subtotal-${item_id}`).val())
            });

            let has_invoice_special_discount_percent = Number($(`#special-discount-percent`).val())
            let has_invoice_special_discount_amount = Number($(`#special-discount`).val())

            let has_discount_percent = $('#invoice-special-discount-percent').val()
            let has_discount_amount = $('#invoice-special-discount-amount').val()

            if (has_discount_percent === '1' && has_invoice_special_discount_percent > 0 && has_invoice_special_discount_amount > 0) {

                Swal.fire({
                    icon: 'info',
                    title: 'Discount amount already selected. Do you want Discount in percent(%)',
                    showCancelButton: true,
                    confirmButtonText: 'Percent',
                    cancelButtonText: 'Amount'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(`#special-discount`).val(0)

                        $('.special-discount').each(function () {
                            $(`this`).val(0)
                        });

                        let special_discount_percent = $(`#special-discount-percent`).val()

                        global_discount = percentCalculation(total_price, special_discount_percent)

                        $('.special-discount').each(function () {
                            item_id = $(this).attr('id').split('-')[2];
                            let item_subtotal = $(`#item-subtotal-${item_id}`).val()

                            let item_ratio = (item_subtotal / total_price) * 100

                            let item_discount = percentCalculation(global_discount, item_ratio)

                            $(`#item-discount-${item_id}`).val(item_discount.toFixed(2))
                        });

                        $(`#has-special-discount`).val(1)

                        getDiscountTotal()
                        getTotal()

                    } else {
                        return
                    }
                })
            }
            else if (has_discount_percent === '1' && has_invoice_special_discount_percent > 0) {
                let special_discount_percent = $(`#special-discount-percent`).val()

                global_discount = percentCalculation(total_price, special_discount_percent)

                $('.special-discount').each(function () {
                    item_id = $(this).attr('id').split('-')[2];
                    let item_subtotal = $(`#item-subtotal-${item_id}`).val()

                    let item_ratio = (item_subtotal / total_price) * 100

                    let item_discount = percentCalculation(global_discount, item_ratio)

                    $(`#item-discount-${item_id}`).val(item_discount.toFixed(2))
                });
                $(`#has-special-discount`).val(1)
            }
            else if (has_discount_amount === '1' && has_invoice_special_discount_amount > 0 && has_invoice_special_discount_percent > 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Discount percent(%) already selected. Do you want Discount in amount',
                    showCancelButton: true,
                    confirmButtonText: 'Amount',
                    cancelButtonText: 'Percent',

                }).then((result) => {
                    if (result.isConfirmed) {

                        $(`#special-discount-percent`).val(0)

                        let invoice_discount_amount = $('#special-discount').val()

                        if (invoice_discount_amount > total_price) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: "Value can't be more then subtotal",
                            });

                            $('#special-discount').val(0)

                            return;
                        }
                        else {
                            let special_discount_amount = $(`#special-discount`).val()

                            global_discount = special_discount_amount;

                            $('.special-discount').each(function () {
                                item_id = $(this).attr('id').split('-')[2];
                                let item_subtotal = $(`#item-subtotal-${item_id}`).val()

                                let item_ratio = (item_subtotal / total_price) * 100

                                let item_discount = percentCalculation(global_discount, item_ratio)

                                $(`#item-discount-${item_id}`).val(item_discount.toFixed(2))
                            });

                            $(`#has-special-discount`).val(1)
                        }

                        getDiscountTotal()
                        getTotal()
                    } else {
                        return
                    }
                })
            }
            else if (has_discount_amount === '1' && has_invoice_special_discount_amount > 0) {

                let invoice_discount_amount = $('#special-discount').val()

                if (invoice_discount_amount > total_price) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: "Value can't be more then subtotal",
                    });

                    $('#special-discount').val(0)

                    return;
                }
                else {
                    $(`#special-discount-percent`).val(0)
                    let special_discount_amount = $(`#special-discount`).val()
                    global_discount = special_discount_amount;

                    $('.special-discount').each(function () {
                        item_id = $(this).attr('id').split('-')[2];
                        let item_subtotal = $(`#item-subtotal-${item_id}`).val()

                        let item_ratio = (item_subtotal / total_price) * 100

                        let item_discount = percentCalculation(global_discount, item_ratio)

                        $(`#item-discount-${item_id}`).val(item_discount.toFixed(2))
                    });
                    $(`#has-special-discount`).val(1)
                }
            }

            getDiscountTotal()
            getTotal()
        }

        function percentCalculation(total, ratio){
            return total*(ratio/100)
        }

        function discountPercent(){
            let amim_id = $('#special-discount-id').val()

            let percent = $(`#special-discount-percent-${amim_id}`).val();

            let item_subtotal = Number($(`#item-subtotal-${amim_id}`).val())

            let discount = item_subtotal * (percent/100);

            let hasAmount = Number($(`#special-discount-amount-${amim_id}`).val())|0

            if(percent > 100){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: "Value can't be more then 100%",
                });

                $(`#special-discount-${amim_id}`).val(0)
                $(`#special-discount-percent-${amim_id}`).val(0);

                return;
            }

            if (hasAmount > 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Discount amount already selected. Do you want Discount in percent(%)',
                    showCancelButton: true,
                    confirmButtonText: 'Percent',
                    cancelButtonText: 'Amount',

                }).then((result) => {
                    if (result.isConfirmed) {
                        $(`#special-discount-amount-${amim_id}`).val('')
                        $(`#special-discount-${amim_id}`).val(discount.toFixed(2))
                        $(`#special-disc-info-${amim_id}`).val(`1:${percent}:${discount.toFixed(2)}`)
                        $('#item-special-discount').val(1)
                        $(`#has-special-discount`).val(1)
                    } else {
                        return
                    }
                })
            }
            else {
                $(`#special-discount-${amim_id}`).val(discount.toFixed(2))
                $(`#special-disc-info-${amim_id}`).val(`1:${percent}:${discount.toFixed(2)}`)
                $('#item-special-discount').val(1)
                $(`#has-special-discount`).val(1)
            }
        }

        function discountAmount(){
            let amim_id = $('#special-discount-id').val()
            let amount = $(`#special-discount-amount-${amim_id}`).val()
            let item_subtotal = Number($(`#item-subtotal-${amim_id}`).val())

            let hasPercent = Number($(`#special-discount-percent-${amim_id}`).val())|0

            if(amount > item_subtotal){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: "Discount can't be more than Order Amount",
                });

                $(`#special-discount-${amim_id}`).val(0)
                $(`#special-discount-amount-${amim_id}`).val(0)

                return;
            }

            if(hasPercent > 0){
                Swal.fire({
                    icon: 'info',
                    title: 'Discount percent(%) already selected. Do you want Discount in amount',
                    showCancelButton: true,
                    confirmButtonText: 'Amount',
                    cancelButtonText: 'Percent',

                }).then((result) => {
                    if (result.isConfirmed) {
                        $(`#special-discount-percent-${amim_id}`).val('')
                        $(`#special-discount-${amim_id}`).val(amount)
                        $(`#special-disc-info-${amim_id}`).val(`2:${amount}`)
                        $('#item-special-discount').val(1)
                        $(`#has-special-discount`).val(1)
                    }else{
                        return
                    }
                })
            }
            else{
                $(`#special-discount-${amim_id}`).val(amount)
                $(`#special-disc-info-${amim_id}`).val(`2:${amount}`)
                $('#item-special-discount').val(1)
                $(`#has-special-discount`).val(1)
            }
        }

        function resetSpecialDiscount(){
            $('#special-discount-id').removeAttr("value")
            let amim_id = $('#special-discount-id').val()
            $(`#special-discount-percent-${amim_id}`).removeAttr("value")
            $(`#special-discount-amount-${amim_id}`).removeAttr("value")
            $(`#special-discount-${amim_id}`).removeAttr("value")
        }

        function showSpecialDiscount(amim_id){
            let subtotal = Number($(`#item-subtotal-${amim_id}`).val())

            if(!subtotal > 0){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please select item quantity or cartoon first !!',
                });
                return;
            }


            let invoice_discount_percent = $('#invoice-special-discount-percent').val()
            let invoice_discount_amount = $('#invoice-special-discount-amount').val()

            if(invoice_discount_percent === '1' || invoice_discount_amount === '1') {
                Swal.fire({
                    icon: 'info',
                    title: 'Invoice wise discount already exist! do you want Item wise discount',
                    showCancelButton: true,
                    confirmButtonText: 'Item wise discount',
                    cancelButtonText: 'Invoice wise discount',

                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#invoice-special-discount-percent').val(0)
                        $('#invoice-special-discount-amount').val(0)
                        $('#special-discount-percent').val(0)
                        $('#special-discount').val(0)
                        $('.special-discount').each(function () {
                            $(this).val(0);
                        });

                        let alreadyHasDiscount = Number($(`#item-discount-${amim_id}`).val())

                        if(alreadyHasDiscount > 0){
                            let discount_infos = $(`#special-disc-info-${amim_id}`).val()
                            let info = discount_infos.split(':')

                            let type    = info[0]

                            Swal.fire({
                                icon: 'info',
                                title: 'Already has discount. Do you want to modify',
                                showCancelButton: true,
                                confirmButtonText: 'Yes',
                                cancelButtonText: 'no',

                            }).then((result) => {
                                if (result.isConfirmed) {
                                    showDiscountModal(amim_id)
                                }else{
                                    return
                                }
                            })
                        }else{
                            showDiscountModal(amim_id)
                        }
                    }
                })
            }else{
                let alreadyHasDiscount = Number($(`#item-discount-${amim_id}`).val())

                if(alreadyHasDiscount > 0){
                    let discount_infos = $(`#special-disc-info-${amim_id}`).val()
                    let info = discount_infos.split(':')

                    let type    = info[0]

                    Swal.fire({
                        icon: 'info',
                        title: 'Already has discount. Do you want to modify',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'no',

                    }).then((result) => {
                        if (result.isConfirmed) {
                            showDiscountModal(amim_id)
                        }else{
                            return
                        }
                    })
                }else{
                    showDiscountModal(amim_id)
                }
            }


        }

        function showDiscountModal(amim_id){
            $('#special-discount-id').val(amim_id)
            $('#special-discount-fields').html(`
            <div style="display: flex; gap: 17px; align-items: center; text-align: start!important; margin-bottom: 5px">
                <span style=" width: 25%; text-align: end;margin-bottom: 6px;"> Percent </span>
                    <div class="input-group" style="display: flex;margin-bottom: 0px!important">

                        <input type="number" style="width: 59px;text-align: end;padding-right: 21px !important;" id="special-discount-percent-${amim_id}"
                               onclick="discountPercent()" oninput="discountPercent()" class="in_tg">

                        <div class="input-group-append" style="position: absolute; bottom: 4px; right: 5px;">
                            <span class="input-group-text"><i class="fa fa-percent"></i></span>
                        </div>
                    </div>
                    <span style="width: 7%;">OR</span>
                    <span style="width: 12%;">Amount</span>

                <input type="number" onclick="discountAmount()" oninput="discountAmount()" id="special-discount-amount-${amim_id}"
                    style="width: 9%;text-align: end;padding: 0;height: 27px;" class="in_tg">
            </div
            <div>
               <span style="width: 12%;margin-bottom: 5px">Discount</span>
               <input type="number" id="special-discount-${amim_id}" readonly
                    style="width: 12%; text-align: end; padding: 0; height: 27px;" class="in_tg">
            </div>`)

            let alreadyHasDiscount = Number($(`#item-discount-${amim_id}`).val())

            if(alreadyHasDiscount > 0) {
                let discount_infos = $(`#special-disc-info-${amim_id}`).val()
                let info = discount_infos.split(':')

                let type = info[0]

                if (type === '1') {
                    let percent = info[1]
                    let amount = info[2]

                    $(`#special-discount-percent-${amim_id}`).val(Number(percent))
                    $(`#special-discount-${amim_id}`).val(Number(amount))
                } else {
                    let amount = info[1]

                    $(`#special-discount-amount-${amim_id}`).val(Number(amount))
                    $(`#special-discount-${amim_id}`).val(Number(amount))
                }
            }

            $("#showSpecialDiscount").show();
            $("#showSpecialDiscount").modal({backdrop: false});
            $('#showSpecialDiscount').modal('show');
        }

        function specialDiscountCalculation(){
            let amim_id = $('#special-discount-id').val()

            let line_discount = $(`#special-discount-${amim_id}`).val()
            $(`#item-discount-${amim_id}`).val(line_discount)

            $('#showSpecialDiscount').modal('hide');

            resetSpecialDiscount()
            getDiscountTotal()
            getTotal()
        }

        function resetInvoiceSpecialDiscount(){
            $('#item-special-discount').removeAttr("value")
            $('#invoice-special-discount-percent').removeAttr("value")
            $('#invoice-special-discount-amount').removeAttr("value")
            $('#has-special-discount').removeAttr("value")

            getDiscountTotal()
            getTotal()
        }


        // GRV Section

        function enableGrv(){
            if($('#has-grv').is(':checked')) {
                $('#has-grv').val(1)
            }else{
                $('#has-grv').val(0)
            }
        }

        function grvItemsAdd(infos, country_id){

            let items = '';
            let amim_id = infos[0]
            let amim_name = infos[1]
            let amim_code = infos[2]
            let amim_price = infos[5]
            let amim_ctn_size = infos[4]

            let item_lists = $('#grv-items-list')

            if(country_id !== '2' || country_id !== '5') {
                items = `<tr id="grv-item-row-${amim_id}" style="color: #0E0EFF">
                            <td>
                                ${amim_code}-${amim_name}
                                <input type="hidden" value="${amim_price}" name="grv_item_unit_prices[]"  id="grv-item-price-${amim_id}">
                                <input type="hidden"  name="grv_item_dufts[]" value="${amim_ctn_size}" id="grv-ctn-size-${amim_id}">
                            </td>
                            <td>
                                <input type="hidden" value="${amim_id}" class="added-grv-items-id"  id="grv-item-id-${amim_id}" name="grv_item_ids[]">
                                <input type="hidden" id="grv-total-item-${amim_id}" name="grv_total_qtys[]">
                                <input type="number" class="in_tg" id="grv-item-qty-${amim_id}" style="width: 90%; text-align: end;" onclick="getGrvSubTotal('${amim_id}')" onkeyup="getGrvSubTotal('${amim_id}')" name="grv_item_qtys[]">
                            </td>
                            <td>
                                <input type="hidden" id="grv-total-ctn-${amim_id}">
                                <input type="number" class="item-id in_tg" id="grv-item-ctn-${amim_id}" style="width: 90%; text-align: end;" onclick="getGrvSubTotal('${amim_id}')" onkeyup="getGrvSubTotal('${amim_id}')" name="grv_item_ctns[]">
                            </td>
                            <td>
                                <input type="number" readonly class="grv-item-subtotal in_tg" style="width: 100%; text-align: end;" id="grv-item-subtotal-${amim_id}" name="grv_item_prices[]">
                            </td>
                            <td style="text-align: center">
                                <i class="fa fa-times-circle-o text-danger" aria-hidden="true" onclick="deleteRow(this, ${amim_id})"></i>
                            </td>
                         </tr>`
            }

            item_lists.append(items)
        }


        function teleOrderHistory(){
            let is_non_productive_order = $('#non-productive').val()
            $('#tele-order-outlet-search').show()
            $('#order-selection-options').hide()
            $('#np-outlet-selection').hide()
            $('#non-productive-option').hide()
            $('#non-productive').val(2)
            $('#order-options').css('justify-content', 'space-around')
            $('.order-file').show()
            $('.non-productive-field').hide();
            $('#non-productive-type').val(null).trigger("change");
            $('#order').prop('checked', true);
            $('#outlet-serial').empty()
            $('#next').text('update')
            $('#next').attr('onclick', 'updateOrder()')
            $('#next').removeClass('btn-info').addClass('btn-primary')
            $('#tracking-id').removeAttr("value")
            $('#outlet-id').removeAttr("value")
            $('#promotion-zone-id').removeAttr("value")
            $('#outlet-selection').hide()
            $('#item-footer').show()
            $('#outlet-id').val('')
            $('#grv-exist').val(0)
            loadOrderedOutlets()
        }

        function loadOrderedOutlets(){
            let country_id = $('#country_id').val()

            $('#cat-id').empty()
            clearInfo()
            $('#grv-exist').val(0)

            $('#sales-group-id').removeAttr("value")

            if(country_id != '') {
                $.ajax({
                    type: "POST",
                    url: "{{URL::to('/')}}/load-ordered-outlets",
                    data: {
                        _token: token,
                        country_id: country_id,
                    },
                    cache: "false",
                    success: function (data) {
                        let info = data.outlets;
                        if(info.length > 0){
                            let ordered_outlets_option = '<option value="">Select an Outlet</option>'
                            for(let i = 0; i < info.length; i++){
                                ordered_outlets_option += `<option value="${info[i].id}">${info[i].site_code}-${info[i].site_name}</option>`
                            }
                            $('#order-outlets').html(ordered_outlets_option)
                        }
                    }, error: function (error) {
                        $('#order-outlets').empty()

                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: 'No Order Today',
                        });
                    }
                });
            }else{
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please Select a Country !!!',
                });
            }
        }

        function loadOrderHistory(){
            let country_id = $('#country_id').val()
            let outlet_id = $('#order-outlets').val()

            $('#cat-id').empty()
            clearInfo()
            $('#outlet-id').val('')
            $('#grv-exist').val(0)

            $('#grv-id').removeAttr("value")
            $('#special-discount').val('')
            $('#special-discount-percent').val('')
            $('#total-discount').val('')
            $('#total-price').val(0)
            $('#order-history-selection').empty()
            $('#sales-group-id').removeAttr("value")

            if(country_id != '' && outlet_id != '') {
                $.ajax({
                    type: "POST",
                    url: "{{URL::to('/')}}/load-order-history",
                    data: {
                        country_id: country_id,
                        outlet_id: outlet_id,
                        _token: token,
                    },
                    cache: "false",
                    success: function (data) {
                        if(data.outlet_info.length > 0){
                            let info = data.outlet_info[0]
                            $('#outlet-name').val(info.site_name);
                            $('#outlet-name').attr('title',info.site_name);
                            $('#outlet-code').val(info.site_code);
                            $('#outlet-code').attr('title',info.site_code);
                            $('#outlet-district').val(info.district);
                            $('#outlet-district').attr('title',info.district);
                            $('#outlet-thana').val(info.thana);
                            $('#outlet-thana').attr('title',info.thana);
                            $('#outlet-mobile').val(info.mobile);
                            $('#outlet-address').val(info.site_adrs);
                            $('#whats-app').attr('href', `https://wa.me/${info.mobile}`);
                            $('#skype-app').attr('href', `skype:${info.mobile}?call`);
                        }
                        else{
                            clearInfo()
                        }

                        if(data.orders.length > 0){

                            let order_history;
                            let orders = data.orders;

                            order_history = `<option value="">Select Order</option>`

                            if(orders.length === 1){
                                order_history += `<option value="${orders[0].id}" selected>${orders[0].ordm_ornm}</option>`
                            }else{
                                for(let i=0; i<orders.length; i++) {
                                    order_history += `<option value="${orders[i].id}">${orders[i].ordm_ornm}</option>`
                                }
                            }

                            $('#order-history-selection').html(order_history)
                        }
                        else{
                            $('#order-history-selection').empty()
                        }

                        if(data.orders.length > 0) {
                            getSubCategories(outlet_id, country_id, data.orders[0].slgp_id, data.orders[0].sr_id, data.orders[0].dlrm_id)
                        }

                        if(data.orders.length === 1 && data.order_info.length > 0) {
                            $('#outlet-id').val(outlet_id)
                            $('#sales-group-id').val(data.orders[0].slgp_id)
                            $('#item-footer').show()
                            let order_infos = data.order_info
                            loadOrderItems(order_infos, country_id)
                        }

                        if(data.grv_info.length > 0) {


                            $('#grv-items-list').empty()
                            $('#grv-exist').val(1)

                            let grv_items = data.grv_info

                            $('#grv-id').val(grv_items[0].rtan_id)

                            let item_lists = $('#grv-items-list')

                            let items = ''

                            if(country_id !== '2' || country_id !== '5') {
                                for (let i = 0; i < grv_items.length; i++) {

                                    items += `<tr id="grv-item-row-${grv_items[i].amim_id}" style="color: #0E0EFF">
                                        <td>
                                            ${grv_items[i].amim_code}-${grv_items[i].amim_name}
                                            <input type="hidden" value="${grv_items[i].rtdd_uprc}" name="grv_item_unit_prices[]"  id="grv-item-price-${grv_items[i].amim_id}">
                                            <input type="hidden"  name="grv_item_dufts[]" value="${grv_items[i].rtdd_duft}" id="grv-ctn-size-${grv_items[i].amim_id}">
                                        </td>
                                        <td>
                                            <input type="hidden" value="${grv_items[i].amim_id}" class="added-grv-items-id"  id="grv-item-id-${grv_items[i].amim_id}" name="grv_item_ids[]">
                                            <input type="hidden" value="${grv_items[i].rtdd_qnty}" id="grv-total-item-${grv_items[i].amim_id}" name="grv_total_qtys[]">
                                            <input type="number" class="in_tg" id="grv-item-qty-${grv_items[i].amim_id}" style="width: 90%; text-align: end;"
                                                onclick="getGrvSubTotal('${grv_items[i].amim_id}')" onkeyup="getGrvSubTotal('${grv_items[i].amim_id}')"
                                                name="grv_item_qtys[]" value="${grv_items[i].rtdd_qnty % grv_items[i].rtdd_duft}">
                                        </td>
                                        <td>
                                            <input type="hidden" id="grv-total-ctn-${grv_items[i].amim_id}">
                                            <input type="number" class="item-id in_tg" id="grv-item-ctn-${grv_items[i].amim_id}" style="width: 90%; text-align: end;"
                                                onclick="getGrvSubTotal('${grv_items[i].amim_id}')" onkeyup="getGrvSubTotal('${grv_items[i].amim_id}')"
                                                name="grv_item_ctns[]" value="${Math.floor(grv_items[i].rtdd_qnty / grv_items[i].rtdd_duft)}">
                                        </td>
                                        <td>
                                            <input type="number" readonly class="grv-item-subtotal in_tg" style="width: 100%; text-align: end;"
                                                id="grv-item-subtotal-${grv_items[i].amim_id}" name="grv_item_prices[]" value="${grv_items[i].rtdd_oamt}">
                                        </td>
                                        <td style="text-align: center">
                                            <i class="fa fa-times-circle-o text-danger" aria-hidden="true" onclick="deleteRow(this, ${grv_items[i].amim_id})"></i>
                                        </td>
                                     </tr>`
                                }
                            }

                            item_lists.append(items)

                            $('#grv-item-footer').show()
                            getGrvTotal()
                        }
                        else{
                            $('#grv-exist').val(0)
                            $('#grv-id').removeAttr("value")
                        }
                    },

                    error: function (error) {
                        clearInfo()
                        $('#grv-items-list').empty()
                        $('#grv-exist').val(0)

                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: error.responseJSON.error,
                        });
                    }
                });
            }
        }


        function loadOrderItems(order_items, country_id){
            let promotion
            let has_promotion
            let special_discount
            let promotion_qty
            let promotion_method
            let delete_method
            let is_readonly

            let item_list = ''

            for (let i = 0; i < order_items.length; i++) {
                is_readonly = (order_items[i].prom_id > 0) ? `readonly` : '';
                has_promotion = (order_items[i].prom_id > 0) ? `class="promotion-row-${order_items[i].prom_id}"` : '';
                promotion = (order_items[i].prom_id > 0) ? `<input type="number" readonly value="${order_items[i].ordd_opds}"
                    class="item-discount prom-item-discount-${order_items[i].prom_id} in_tg" readonly
                    style="width: 100%; text-align: end;" id="item-discount-${order_items[i].amim_id}"
                    name="item_discounts[]">` : `<input type="hidden" name="item_discounts[]" value="0">`
                special_discount = (order_items[i].prom_id === 0) ? `<input type="number" class="item-discount special-discount in_tg"
                    name="item_special_discounts[]" readonly onclick="showSpecialDiscount('${order_items[i].amim_id}')" value="${order_items[i].ordd_spdi}"
                    style="width: 100%; text-align: end; border: 1px solid blue !important;"
                    id="item-discount-${order_items[i].amim_id}">` : `<input type="hidden" name="item_special_discounts[]" value="0">`
                promotion_qty = (order_items[i].prom_id > 0) ? `class="promotion-item-${order_items[i].prom_id}"` : ''
                promotion_method = (order_items[i].prom_id > 0) ? ``
                    : `onclick="getSubTotal('${order_items[i].amim_id}')" onkeyup="getSubTotal('${order_items[i].amim_id}')"`
                delete_method = (order_items[i].prom_id > 0) ? `onclick="deletePromotionRows(this, ${order_items[i].prom_id})"` : `onclick="deleteRow(this, ${order_items[i].amim_id})"`

                if (country_id !== '2' || country_id !== '5') {
                    if(order_items[i].ordd_smpl === 0) {

                        item_list += `<tr id="item-row-${order_items[i].amim_id}" ${has_promotion}>
                            <td>
                                ${order_items[i].amim_code}-${order_items[i].amim_name}
                                <input type="hidden" value="${order_items[i].amim_id}" class="added-items-id"  id="item-id-${order_items[i].amim_id}" name="item_ids[]">
                                <input type="hidden" value="${order_items[i].ordd_uprc}" name="item_unit_prices[]"  id="item-price-${order_items[i].amim_id}">
                                <input type="hidden" name="item_dufts[]" value="${order_items[i].ordd_duft}" id="ctn-size-${order_items[i].amim_id}">
                            </td>
                            <td>
                                <input type="hidden" ${promotion_qty} value="${order_items[i].ordd_inty}" id="total-item-${order_items[i].amim_id}" name="total_qtys[]">
                                <input type="number" class="in_tg" id="item-qty-${order_items[i].amim_id}" style="width: 90%; text-align: end;"
                                    ${promotion_method} ${is_readonly} value="${order_items[i].ordd_inty % order_items[i].ordd_duft}">
                            </td>
                            <td>
                                <input type="number" class="item-id in_tg" id="item-ctn-${order_items[i].amim_id}" style="width: 90%; text-align: end;"
                                    ${promotion_method} ${is_readonly} value="${Math.floor(order_items[i].ordd_inty / order_items[i].ordd_duft)}">
                            </td>
                            <td>
                                <input type="hidden" id="special-disc-info-${order_items[i].amim_id}">
                                <input type="hidden" value="0" id="item-discount-prom-${order_items[i].amim_id}"
                                    name="item_prom_ids[${order_items[i].amim_id}]">
                                    ${promotion}
                                    ${special_discount}
                            </td>
                            <td>
                                <input type="number" readonly class="item-subtotal in_tg" value="${order_items[i].ordd_oamt}"
                                    style="width: 100%; text-align: end;border: 1px solid #5bf728 !important;"
                                    id="item-subtotal-${order_items[i].amim_id}" name="item_prices[]">
                            </td>
                            <td style="text-align: center">
                                <i class="fa fa-times-circle-o text-danger" aria-hidden="true" ${delete_method}></i>
                            </td>
                        </tr>`
                    }
                    else if(order_items[i].ordd_smpl === 1){
                        item_list += `
                            <tr id="free-item-${order_items[i].prom_id}" class="promotion-row-${order_items[i].prom_id}"
                                style="color: #0036fb;">
                            <td>
                                ${order_items[i].amim_code}-${order_items[i].amim_name}

                                <input type="hidden" value="${order_items[i].prom_id}" class="added-items-prom-id"
                                    name="free_item_prom_ids[]">
                                <input type="hidden" value="${order_items[i].pldt_tppr}" name="free_item_unit_prices[]">
                                <input type="hidden" value="${order_items[i].amim_duft}" name="free_item_dufts[]">
                                <input type="hidden" value="${order_items[i].amim_id}" id="free-item-id-${order_items[i].amim_id}"
                                    name="free_item_ids[]">
                            </td>
                            <td>
                                <input type="number" value="${order_items[i].ordd_inty}" name="free_item_qtys[]" readonly
                                        class="in_tg" id="free-item-qty-${order_items[i].amim_id}" style="width: 90%; text-align: end;">
                            </td>
                        </tr>`
                    }
                }
            }


            $('#items-list').html(item_list)

            getTotal()
            getDiscountTotal()
        }


        function updateOrder(){
            let order_info = new FormData($('#tele-order').get(0));

            let totalGrv = $('.added-grv-items-id').length
            let totalOrderItems = $('.added-items-id').length

            let orderAmount = Number($('#total-price').val())

            let grvTotalAmount = 0
            let specialDiscountAmount = 0

            $('.grv-item-subtotal').each(function (){
                grvTotalAmount+=Number($(this).val())
            })

            $('.special-discount').each(function (){
                specialDiscountAmount+=Number($(this).val())
            })

            if(specialDiscountAmount > 0){
                order_info.set('has_special_discount', '1')
            }else{
                order_info.set('has_special_discount', '0')
            }

            let ordm_id = $('#order-history-selection').val()
            let slgp_id = $('#sales-group-id').val()
            order_info.append('ordm_id', ordm_id)
            order_info.set('slgp_id', slgp_id)


            let country_id = $('#country_id').val();

            if((country_id === '3') && orderAmount > 0 && orderAmount < 30){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: `Order Amount (${orderAmount}) must be more than 30`,
                });
                return;
            }
            else if((country_id === '2' || country_id === '14') && orderAmount > 0 && orderAmount < 48){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: `Order Amount (${orderAmount}) must be more than 48`,
                });
                return;
            }
            else if((country_id !== '2' && country_id !== '3' && country_id !== '14') && orderAmount > 0 && orderAmount < 100){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: `Order Amount (${orderAmount}) must be more than 100`,
                });
                return;
            }
            else if(totalGrv > 0 && totalOrderItems === 0){
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Order at least one item to have GRV',
                });
            }
            else if(totalGrv > 0 && orderAmount < grvTotalAmount){
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: `GRV Amount (${grvTotalAmount}) can not be more than Order Amount (${orderAmount})`,
                });
            }
            else {
                $.ajax({
                    type: "POST",
                    url: "{{URL::to('/')}}/update-order",
                    data: order_info,
                    processData: false,
                    contentType: false,
                    cache: "false",
                    success: function (data) {
                        clearOrderUpdate()
                        teleOrderHistory()
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Order Updated Successfully',
                        });
                    },
                    error: function (error) {
                        console.log(error.code)
                    }
                });
            }
        }

        function clearOrderUpdate(){
            clearInfo()
            $('#items-list').empty()
            $('#grv-exist').val(0)
            $('#outlet-id').val('')
            $('#special-discount').val('')
            $('#special-discount-percent').val('')
            $('#total-discount').val('')
            $('#total-price').val(0)
            $('#order-history-selection').empty()
            $('#sales-group-id').removeAttr("value")
            $('#grv-items-list').empty()
            $('#grv-id').removeAttr("value")
            $('#np-dlrm-id').removeAttr("value")
            $('#grv-total-price').val(0)
            $('#cat-id').empty()
            $('#grv-cat-id').empty()
            $('#order-outlets').empty()
        }

        function nonProductiveOrder(){
            let is_non_productive_order = $('#non-productive').val()
            if(is_non_productive_order === '0') {
                $('#non-productive-order').text(` ::: Non Productive :::`)
                $('#order-selection-options').show()
                $('#np-outlet-selection').show()
                $('#non-productive-option').hide()
                $('#tele-order-outlet-search').hide()
                $('#non-productive').val(1)
                $('#order-options').css('justify-content', 'space-around')
                $('.order-file').show()
                $('.non-productive-field').hide();
                $('#non-productive-type').val(null).trigger("change");
                $('#order').prop('checked', true);
                $('#outlet-serial').empty()
                $('#next').text('save')
                $('#next').attr('onclick', 'storeOrder()')
                $('#next').removeClass('btn-info').addClass('btn-success')
                $('#tracking-id').removeAttr("value")
                $('#outlet-id').removeAttr("value")
                $('#promotion-zone-id').removeAttr("value")
                $('#outlet-selection').hide()
                $('#np-dlrm-id').val('')

                loadNonProductive()
            }
            else{
                $('#order-selection-options').show()
                $('#tele-order-outlet-search').hide()
                $('#non-productive-option').show()
                $('#non-productive-order').text(` ::: Order :::`)
                $('#np-outlet-selection').hide()
                $('#non-productive').val(0)
                $('#order-options').css('justify-content', 'center')
                $('.order-file').hide()
                $('#order').prop('checked', false);
                $('#next').text('next')
                $('#next').attr('onclick', 'storeTeleOrder()')
                $('#next').removeClass('btn-success').addClass('btn-info')
                $('#tracking-id').removeAttr("value")
                $('#outlet-id').removeAttr("value")
                $('#promotion-zone-id').removeAttr("value")
                $('#outlet-selection').show()
            }
        }

        function loadNonProductive(){
            let country_id = $('#country_id').val()

            if(country_id != '') {
                $.ajax({
                    type: "POST",
                    url: "{{URL::to('/')}}/load-non-productive",
                    data: {
                        country_id: country_id,
                        _token: token,
                    },
                    cache: "false",
                    success: function (data) {

                        if(data.length > 0){
                            let info = data;

                            $('#nop-outlet-id').empty();

                            var html='<option value="" selected>Select Outlet</option>';
                            for(var i=0;i<info.length;i++){
                                html+=`<option value="${info[i].id}:${info[i].site_id}:${info[i].slgp_id}:${info[i].sr_id}:${info[i].zone_id}:${info[i].dlrm_id}">
                                    ${info[i].site_code}-${info[i].site_name}</option>`;
                            }

                            $('#nop-outlet-id').append(html);
                        }else{
                            $('#nop-outlet-id').empty();
                        }
                    }, error: function (error) {
                        $('#promotion-zone-id').removeClass("value")

                        // if(){
                        Swal.fire({
                            icon: 'warning',
                            title: 'Failed',
                            text: error.responseJSON.error,
                        });
                        // }
                    }
                });
            }else{
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please Select a Country !!!',
                });
            }

        }

        function loadNopOutletInfo(){
            let selected_outlet = $('#nop-outlet-id').select2('data')[0]
            let outlet_info   = selected_outlet.id

            let information = outlet_info.split(':')
            let id          = information[0]
            let outlet_id   = information[1]
            let slgp_id     = information[2]
            let sr_id       = information[3]
            let zone_id     = information[4]
            let dlrm_id     = information[5]
            $('#tracking-id').val(id)
            $('#promotion-zone-id').val(zone_id)
            $('#np-dlrm-id').val(dlrm_id)



            getOutletInfo(outlet_id, sr_id);
            $('#order-details-list').empty()
            getSubCategories(outlet_id, null, slgp_id, sr_id, dlrm_id)
        }

        function isNonProductive() {
            if($('#non_productive').is(":checked")){
                $('#order').prop('checked', false);
                $('.non-productive-field').show();
                $('.order-file').hide()
                $('#items-list').empty()
                $('#total-price').val(0)
            }else if($('#order').is(":checked")){
                $('#non_productive').prop('checked', false);
                $('#non-productive-type').val(null).trigger("change");
            }else{
                $('.non-productive-field').hide();
            }
        }

        function storeOrder(){
            let order_info = new FormData($('#tele-order').get(0));

            let audio_files = $('#recording-file')[0].files

            let totalGrv = $('.added-grv-items-id').length
            let totalOrderItems = $('.added-items-id').length

            let orderAmount = Number($('#total-price').val())

            let grvTotalAmount = 0

            $('.grv-item-subtotal').each(function (){
                grvTotalAmount+=Number($(this).val())
            })


            let outlet_id =  $('#outlet-id').val()

            let country_id = $('#country_id').val();

            if((country_id === '3') && orderAmount > 0 && orderAmount < 30){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: `Order Amount (${orderAmount}) must be more than 30`,
                });
                return;
            }
            else if((country_id === '2' || country_id === '14') && orderAmount > 0 && orderAmount < 48){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: `Order Amount (${orderAmount}) must be more than 48`,
                });
                return;
            }
            else if((country_id !== '2' && country_id !== '3' && country_id !== '14') && orderAmount > 0 && orderAmount < 100){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: `Order Amount (${orderAmount}) must be more than 100`,
                });
                return;
            }
            else if(need_audio && audio_files.length === 0){
                Swal.fire({
                    icon: 'info',
                    title: 'Voice clip is missing, want to continue',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',

                }).then((result) => {
                    if (result.isConfirmed) {
                        if((country_id === '3') && orderAmount > 0 && orderAmount < 30){
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: `Order Amount (${orderAmount}) must be more than 30`,
                            });
                            return;
                        }
                        else if((country_id === '2' || country_id === '14') && orderAmount > 0 && orderAmount < 48){
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: `Order Amount (${orderAmount}) must be more than 48`,
                            });
                            return;
                        }
                        else if((country_id !== '2' && country_id !== '3' && country_id !== '14') && orderAmount > 0 && orderAmount < 100){
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: `Order Amount (${orderAmount}) must be more than 100`,
                            });
                            return;
                        }
                        else if(totalGrv > 0 && totalOrderItems > 0 && orderAmount < grvTotalAmount){
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: `GRV Amount (${grvTotalAmount}) can not be more than Order Amount (${orderAmount})`,
                            });

                            return;
                        }
                        else if(totalGrv > 0 && totalOrderItems === 0){
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: 'Order at least one item to have GRV',
                            });
                            return;
                        }
                        else{
                            let order_info = $('#tele-order').serialize()

                            $.ajax({
                                type:"POST",
                                url:"{{URL::to('/')}}/tele-order-non-productive",
                                data: order_info,
                                cache:"false",
                                success:function(data){

                                    $('#cat-id').empty()
                                    $('#items-list').empty()
                                    $('#nop-outlet-id').empty()
                                    $('#total-price').val(0)
                                    $('#order-details-list').empty()
                                    $('#tracking-id').removeClass("value")

                                    clearInfo()

                                    loadNonProductive()

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: 'Non Productive Order Stored Successfully',
                                    });

                                },error:function(error) {

                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Failed',
                                        text: error.responseJSON.error,
                                    });
                                }
                            });
                        }
                    }
                })
            }
            else if(need_audio && audio_files.length > 0 && audio_files[0].size > 5*1024*1024){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'File size must be less than 5MB',
                });
            }
            else if(need_audio && audio_files.length > 0 && !audio_files[0].type.startsWith("audio")){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'File type must be an audio',
                });
            }
            else if(totalGrv > 0 && totalOrderItems === 0){
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Order at least one item to have GRV',
                });
            }
            else if(totalGrv > 0 && orderAmount < grvTotalAmount){
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: `GRV Amount (${grvTotalAmount}) can not be more than Order Amount (${orderAmount})`,
                });
            }
            else{

                let order_info = $('#tele-order').serialize()


                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/tele-order-non-productive",
                    data: order_info,
                    cache:"false",
                    success:function(data){

                        $('#cat-id').empty()
                        $('#items-list').empty()
                        $('#nop-outlet-id').empty()
                        $('#total-price').val(0)
                        $('#order-details-list').empty()
                        $('#tracking-id').removeClass("value")

                        clearInfo()

                        loadNonProductive()

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Non Productive Order Stored Successfully',
                        });

                    },error:function(error) {

                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: error.responseJSON.error,
                        });
                    }
                });
            }
        }

        function isOrder() {
            if($('#order').is(":checked")){
                $('#non_productive').prop('checked', false);
                $('.order-file').show()
                $('#non-productive').val(0)
                $('.non-productive-field').hide();
                $('#non-productive-type').val(null).trigger("change");

            }else{
                $('.order-file').hide()
            }
        }

        let note_shown = false

        function showNote() {
            note_shown = !note_shown

            if(note_shown){
                $(".note-field").show()
                $("#items").css('height','16vh')
                $("#grv-items").css('height','16.2vh')
                $("#has-note").attr("checked", true)
            }else{
                $(".note-field").hide()
                $("#items").css('height','19.5vh')
                $("#grv-items").css('height','19.5vh')
                $("#has-note").attr("checked", false);
                $("#note").val('')
                $('#recording-file').val('')
                $("#note-type").val(null).trigger("change");
            }
        }

        function getCompanies(){
            let country = $('#country-id').select2('data')[0]
            let country_id = country.id
            outlets = []
            $('#outlet-serial').empty()
            $('#country_id').val('')
            $('#order-outlet-id').empty()
            $('#grv-cat-id').empty();


            clearInfo()
            $('#amim-id').empty();
            $('#cat-id').empty();
            $("#employee-id").val('')
            $('#plmt-id').val('')
            $('#sales-group-id').val('')
            $('#acmp-id').html(`<option value="" selected>Select BU</option>`)
            $('#slgp-id').html(`<option value="" selected>Select Group</option>`)
            $('#zone-id').html(`<option value="" selected>Select Zone</option>`)
            $('#sr-id').html(`<option value="" selected>Select SR</option>`)
            $('#dlrm-id').html(`<option value="" selected>Select Distributor</option>`)
            $('#rout-id').html(`<option value="" selected>Select Rout</option>`)
            $('#outlet-id').val('')

            $('#country_id').val(country_id)

            if(country_id !=''){
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/getCompanies",
                    data:{
                        country_id: country_id,
                        _token: token,
                    },
                    cache:"false",
                    success:function(data){

                        let info = data.companies
                        let con = data.con

                        $('#con').val(con)


                        $('#acmp-id').empty();
                        var html='<option value="" selected>Select BU</option>';
                        if(data){
                            for(var i=0;i<info.length;i++){
                                html+='<option value="'+info[i].id+'">'+info[i].acmp_code+'-'+info[i].acmp_name+'</option>';
                            }
                        }
                        $('#acmp-id').append(html);
                    },error:function(error) {
                        if(error.responseJSON.hasOwnProperty('exist') && error.responseJSON.exist === 0){
                            Swal.fire({
                                icon: 'warning',
                                title: 'Failed',
                                text: error.responseJSON.error,
                            });
                        }
                    }
                });
            }
        }

        function getGroups(){
            let company = $('#acmp-id').select2('data')[0]
            let company_id = company.id
            let con = $('#con').val()
            $('#outlet-serial').empty()
            outlets = []
            clearInfo()
            $('#plmt-id').val('')
            $('#amim-id').empty();
            $("#employee-id").val('')
            $('#sales-group-id').val('')
            $('#slgp-id').html(`<option value="" selected>Select Group</option>`)
            $('#zone-id').html(`<option value="" selected>Select Zone</option>`)
            $('#sr-id').html(`<option value="" selected>Select SR</option>`)
            $('#dlrm-id').html(`<option value="" selected>Select Distributor</option>`)
            $('#rout-id').html(`<option value="" selected>Select Rout</option>`)
            $('#outlet-id').val('')
            $('#cat-id').empty();
            $('#order-outlet-id').empty()
            $('#grv-cat-id').empty();



            if(company_id !=''){

                getZones()


                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/getGroups",
                    data:{
                        company_id: company_id,
                        con: con,
                        _token: token,
                    },
                    cache:"false",
                    success:function(data){

                        let info = data.groups
                        let con = data.con

                        $('#con').val(con)



                        $('#slgp-id').empty();
                        var html='<option value="" selected>Select Group</option>';
                        if(info){
                            for(var i=0;i<info.length;i++){
                                html+='<option value="'+info[i].id+'">'+info[i].slgp_code+'-'+info[i].slgp_name+'</option>';
                            }
                        }
                        $('#slgp-id').append(html);
                    }
                });
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please Select a Country First !!!',
                });
            }
        }

        function getNoteNonProductiveTypes(){
            let country = $('#country-id').select2('data')[0]
            let country_id = country.id
            let con = $('#con').val()
            $('#outlet-serial').empty()
            outlets = []
            clearInfo()
            $('#plmt-id').val('')
            $('#amim-id').empty();
            $("#employee-id").val('')
            $('#sales-group-id').val('')
            $('#zone-id').html(`<option value="" selected>Select Zone</option>`)
            $('#sr-id').html(`<option value="" selected>Select SR</option>`)
            $('#rout-id').html(`<option value="" selected>Select Rout</option>`)
            $('#outlet-id').val('')
            $('#cat-id').empty();
            $('#order-outlet-id').empty()


            if(country_id !=''){
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/getNoteNonProductiveTypes",
                    data:{
                        country_id: country_id,
                        con: con,
                        _token: token,
                    },
                    cache:"false",
                    success:function(data){

                        let np_reasons = data.np_reasons
                        let note_types = data.note_types


                        $('#non-productive-type').empty();
                        var html='<option value="" selected>Select a Reason</option>';
                        if(np_reasons){
                            for(var i=0;i<np_reasons.length;i++){
                                html+='<option value="'+np_reasons[i].id+'">'+np_reasons[i].nopr_name+'</option>';
                            }
                        }
                        $('#non-productive-type').append(html);



                        $('#note-type').empty();
                        var html='<option value="" selected>Select a Note Type</option>';
                        if(note_types){
                            for(var i=0;i<note_types.length;i++){
                                html+='<option value="'+note_types[i].id+'">'+note_types[i].ntpe_name+'</option>';
                            }
                        }
                        $('#note-type').append(html);
                    }
                });
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please Select a Country First !!!',
                });
            }
        }

        function storeSalesGroup(){
            let slgp = $('#slgp-id').select2('data')[0]
            let slgp_id = slgp.id
            $('#plmt-id').val('')
            $('#outlet-serial').empty()
            outlets = []
            $('#order-outlet-id').empty()
            $('#cat-id').empty();
            $('#employee-id').empty();
            $('#order-list').empty();
            $('#sr-id').html(`<option value="" selected>Select SR</option>`)
            $('#dlrm-id').html(`<option value="" selected>Select Distributor</option>`)
            $('#rout-id').html(`<option value="" selected>Select Rout</option>`)
            $('#outlet-id').val('')
            $('#sales-group-id').val(slgp_id)
        }

        function getZones(){
            let con = $('#con').val()
            let company = $('#acmp-id').select2('data')[0]
            let company_id = company.id
            $("#employee-id").val('')
            $('#cat-id').empty();
            $('#plmt-id').val('')
            $('#outlet-serial').empty()
            $('#order-outlet-id').empty()
            $('#grv-cat-id').empty();

            $('#special-discount').val('')
            $('#special-discount-percent').val('')
            $('#total-discount').val('')
            $('#total-price').val(0)
            outlets = []

            let country_id = $('#country_id').val()
            clearInfo()
            $('#amim-id').empty();
            $('#zone-id').html(`<option value="" selected>Select Zone</option>`)
            $('#sr-id').html(`<option value="" selected>Select SR</option>`)
            $('#dlrm-id').html(`<option value="" selected>Select Distributor</option>`)
            $('#rout-id').html(`<option value="" selected>Select Rout</option>`)
            $('#outlet-id').val('')

            if(country_id !='' && company_id != ''){
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/getZones",
                    data:{
                        country_id: country_id,
                        company_id: company_id,
                        con: con,
                        _token: token,
                    },
                    cache:"false",
                    success:function(data){

                        let info = data.zones
                        let con = data.con

                        $('#con').val(con)



                        $('#zone-id').empty();
                        var html='<option value="" selected>Select Zone</option>';
                        if(info){
                            for(var i=0;i<info.length;i++){
                                html+='<option value="'+info[i].id+'">'+info[i].zone_code+'-'+info[i].zone_name+'</option>';
                            }
                        }
                        $('#zone-id').append(html);
                    }
                });
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please Select Company and Country First !!!',
                });
            }


        }

        function getSrs(){
            let con = $('#con').val()
            let country_id = $('#country_id').val()
            let zone = $('#zone-id').select2('data')[0]
            let slgp = $('#slgp-id').select2('data')[0]
            $("#employee-id").val('')
            $('#cat-id').empty();
            $('#plmt-id').val('')
            $('#outlet-serial').empty()
            $('#grv-cat-id').empty();

            $('#special-discount').val('')
            $('#special-discount-percent').val('')
            $('#total-discount').val('')
            $('#total-price').val(0)

            $('#sr-id').html(`<option value="" selected>Select SR</option>`)
            $('#rout-id').html(`<option value="" selected>Select Rout</option>`)
            $('#dlrm-id').html(`<option value="" selected>Select Distributor</option>`)
            $('#outlet-id').val('')

            let zone_id  = zone.id
            let slgp_id  = slgp.id
            clearInfo()
            $('#amim-id').empty();
            $('#outlet-id').empty();

            if(con != '' && country_id  != '' && zone_id  != '' && slgp_id  != ''){
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/getSrs",
                    data:{
                        country_id: country_id,
                        con: con,
                        zone_id: zone_id,
                        slgp_id: slgp_id,
                        _token: token,
                    },
                    cache:"false",
                    success:function(data){

                        let info = data.srs
                        let con = data.con

                        $('#con').val(con)



                        $('#sr-id').empty();
                        var html='<option value="" selected>Select SR</option>';
                        if(info){
                            for(var i=0;i<info.length;i++){
                                html+='<option value="'+info[i].id+'">'+info[i].aemp_usnm+'-'+info[i].aemp_name+'</option>';
                            }
                        }
                        $('#sr-id').append(html);
                    }
                });
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please Select a Zone First !!!',
                });
            }

        }

        function getRoutes(){
            let con = $('#con').val()
            let country_id = $('#country_id').val()
            let aemp = $('#sr-id').select2('data')[0]
            $('#outlet-id').val('')
            let aemp_id  = aemp.id
            $('#outlet-serial').empty()
            $('#order-outlet-id').empty()
            $('#grv-cat-id').empty();

            $('#special-discount').val('')
            $('#special-discount-percent').val('')
            $('#total-discount').val('')
            $('#total-price').val(0)
            outlets = []
            clearInfo()
            $('#rout-id').html(`<option value="" selected>Select Rout</option>`)


            if(con != '' && country_id  != '' && aemp_id  != ''){
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/getRoutes",
                    data:{
                        country_id: country_id,
                        con: con,
                        aemp_id: aemp_id,
                        _token: token,
                    },
                    cache:"false",
                    success:function(data){
                        $("#employee-id").val(aemp_id)

                        let info = data.routs
                        let con = data.con

                        $('#con').val(con)



                        $('#rout-id').empty();
                        var html='<option value="" selected>Select Rout</option>';
                        if(info){
                            for(var i=0;i<info.length;i++){
                                html+='<option value="'+info[i].id+'">'+info[i].rout_code+'-'+info[i].rout_name+'</option>';
                            }
                        }
                        $('#rout-id').append(html);
                    }
                });
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please Select a SR !!!',
                });
            }

        }

        function getDistributors(){
            let con = $('#con').val()
            let aemp = $('#sr-id').select2('data')[0]
            $('#dlrm-id').html(`<option value="" selected>Select Distributor</option>`)
            $('#plmt-id').val('')
            let aemp_id  = aemp.id
            $('#outlet-serial').empty()
            $('#grv-cat-id').empty();

            $('#special-discount').val('')
            $('#special-discount-percent').val('')
            $('#total-discount').val('')
            $('#total-price').val(0)
            clearInfo()

            if(con != '' && country_id  != '' && aemp_id  != ''){
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/getDistributors",
                    data:{
                        con: con,
                        aemp_id: aemp_id,
                        _token: token,
                    },
                    cache:"false",
                    success:function(data){
                        $("#employee-id").val(aemp_id)

                        let info = data

                        $('#con').val(con)

                        $('#dlrm-id').empty();
                        var html='<option value="" selected>Select Distributor</option>';
                        if(info){
                            for(var i=0;i<info.length;i++){
                                html+='<option value="'+info[i].id+'">'+info[i].dlrm_code+'-'+info[i].dlrm_name+'</option>';
                            }
                        }
                        $('#dlrm-id').append(html);
                    }
                });
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please Select a SR !!!',
                });
            }

        }

        function loadOutletInfo(){
            let outlet_id = $('#order-outlet-id').val()
            $('#outlet-id').val(outlet_id)
            getOutletInfo(outlet_id);
            $('#order-details-list').empty()
            getSubCategories(outlet_id)
            // $('#msp-block').show()
            getMspData(outlet_id)
        }

        function getOutlets(){
            let con = $('#con').val()
            let country_id = $('#country_id').val()
            let route = $('#rout-id').select2('data')[0]
            let route_id  = route.id
            let employee_id = $('#employee-id').val()
            $('#outlet-serial').empty()
            $('.order-details-table').hide()
            $('#grv-cat-id').empty();

            $('#special-discount').val('')
            $('#special-discount-percent').val('')
            $('#total-discount').val('')
            $('#total-price').val(0)
            clearInfo()

            if(country_id === "9") {
                $('#grv-area').hide();
                $('#items').css('height', '45.5vh');
            }else{
                $('#grv-area').show();
                $('#items').css('height', '19.5vh');
            }

            if(con != '' && country_id  != '' && route_id  != '' && employee_id != ''){
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/getOutlets",
                    data:{
                        country_id: country_id,
                        con: con,
                        route_id: route_id,
                        employee_id: employee_id,
                        _token: token,
                    },
                    cache:"false",
                    success:function(data){

                        outlets         = data.outlets;
                        total_outlet    = outlets.length;

                        $('#outlet-serial').html(`${data.visited_outlet_in_rout} out of ${total_outlet}`)

                        if(data.outlets.length>0)
                        {
                            $('#outlet-id').val(outlets[0].id)
                            getOutletInfo(outlets[0].id);
                            $('#order-details-list').empty()
                            getSubCategories(outlets[0].id)
                            // $('#msp-block').show()
                            getMspData(outlets[0].id)

                            $('#outlet-selection').show()
                            let outlet_options=`<option value="">Select Outlet</option>
                                <option value="${outlets[0].id}" selected>${outlets[0].site_code}-${outlets[0].site_name}</option>`;
                            for(let i=1;i<outlets.length;i++){
                                outlet_options+='<option value="'+outlets[i].id+'">'+outlets[i].site_code+'-'+outlets[i].site_name+'</option>';
                            }
                            $('#order-outlet-id').html(outlet_options);
                        }
                    }
                });
            }else{
                $('#outlet-selection').hide()
                $('#order-outlet-id').empty()

                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please Select a Route First !!!',
                });
            }

        }

        function getMspData(outlet_id){
            let country_id = $('#country-id').val()
            $.ajax({
                type: "POST",
                url: "{{URL::to('/')}}/getMspData",
                data: {
                    _token: token,
                    country_id: country_id,
                    outlet_id: outlet_id,
                },
                cache: "false",
                success: function (data) {
                    if(data.length > 0){
                        $('#msp-block').show()
                        let msp_info = ''
                        for (let i = 0; i < data.length; i++) {
                            msp_info += `<tr>
                                            <td>
                                                <span class="order-list-text">${data[i].amim_code}</span>
                                            </td>
                                            <td>
                                                <span class="order-list-text">${data[i].amim_name}</span>
                                            </td>
                                            <td>
                                                <span class="order-list-text">${data[i].mspd_qnty}</span>
                                            </td>
                                            <td >
                                                <span class="order-list-text">${data[i].order_qty}</span>
                                            </td>
                                            <td >
                                                <span class="order-list-text">${data[i].deliver_qty}</span>
                                            </td>
                                        </tr>`;
                        }

                        $('#msp-history').html(msp_info)

                    }
                },
                error: function (error){
                    $('#msp-block').hide()
                }
            });
        }

        function getOutletInfo(outlet_id = null, employee_id = null){
            let con = $('#con').val()
            let country_id = $('#country_id').val()

            if(employee_id === null) {
                employee_id = $('#employee-id').val()
            }
            $('#outlet-id').val(outlet_id)

            clearInfo()
            $('#order-details-list').empty()

            if(con != '' && country_id  != '' && outlet_id  != '') {
                showOutletInfo(con, country_id, outlet_id)
            }

            if(con != '' && employee_id  != '' && outlet_id  != '') {
                getOrderInfo(con, employee_id, outlet_id)
            }
        }

        function showOutletInfo(con, country_id, outlet_id){
            if(con != '' && country_id  != '' && outlet_id  != ''){

                $('#special-discount').val('')
                $('#special-discount-percent').val('')
                $('#total-discount').val('')
                $('#total-price').val(0)
                clearInfo()
                $('#order-details-list').empty()

                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/getOutletInfo",
                    data:{
                        country_id: country_id,
                        con: con,
                        outlet_id: outlet_id,
                        _token: token,
                    },
                    cache:"false",
                    success:function(data){

                        if(data[0]) {
                            let info = data[0]
                            $('#ajax_load').css('display', 'none');
                            $('#outlet-name').val(info.site_name);
                            $('#outlet-name').attr('title',info.site_name);
                            $('#outlet-code').val(info.site_code);
                            $('#outlet-code').attr('title',info.site_code);
                            $('#outlet-district').val(info.district);
                            $('#outlet-district').attr('title',info.district);
                            $('#outlet-thana').val(info.thana);
                            $('#outlet-thana').attr('title',info.thana);
                            $('#outlet-mobile').val(info.mobile);
                            $('#outlet-address').val(info.site_adrs);
                            $('#whats-app').attr('href', `https://wa.me/${info.mobile}`);
                            $('#skype-app').attr('href', `skype:${info.mobile}?call`);
                        }
                    },error:function(error){
                    }
                });
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please Select a Route First !!!',
                });
            }
        }

        function getOrderInfo(con, employee_id, outlet_id){
            if(con != '' && country_id  != '' && outlet_id  != ''){
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/getOrderInfo",
                    data:{
                        employee_id: employee_id,
                        con: con,
                        outlet_id: outlet_id,
                        _token: token,
                    },
                    cache:"false",
                    success:function(data){



                        if(data && data.length>0){

                            $('#order-history').show()

                            let order_items = '';

                            for(let i=0; i<data.length; i++) {
                                let count = i;

                                let order_details_data = ''


                                let order_details_data_info = ''


                                if(data[i].order_details.length > 0) {


                                    let order_details = data[i].order_details;


                                    for (let j = 0; j < order_details.length; j++) {

                                        order_details_data_info += `
                                        <tr>
                                            <td>
                                                <span class="order-list-text">${order_details[j].amim_code}</span>
                                            </td>
                                            <td>
                                                <span class="order-list-text">${order_details[j].amim_name}</span>
                                            </td>
                                            <td>
                                                <span class="order-list-text">${order_details[j].ordd_inty}</span>
                                            </td>
                                            <td style="text-align: end;">
                                                <span class="order-list-text">${order_details[j].ordd_oamt}</span>
                                            </td>
                                        </tr>`
                                    }

                                }
                                order_details_data +=`
                                <div class="collapse" id="order-details-info-${data[i].id}" aria-expanded="false" style="">
                                    <div class="col-md-12 order-details-table" style="/* overflow-y: scroll; */padding: 0px;/* height: 200px !important; *//* display: none; */">
                                        <table class="table" style="width: 97%; padding-top: 10px; padding-left: 5px; margin: 0">
                                            <thead style="position: sticky; top: 0;">
                                            <tr class="tbl_header_light">
                                                <th style="width: 20%;">CODE</th>
                                                <th>NAME</th>
                                                <th>QTY</th>
                                                <th style="width: 12%;">AMNT</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                ${order_details_data_info}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>`


                                order_items += `
                                <tr>
                                    <td>
                                        <span class="order-list-text">${++count}</span>
                                    </td>
                                    <td>
                                        <span class="order-list-text">${data[i].ordm_date}</span>
                                    </td>
                                    <td>
                                        <span class="order-list-text">${data[i].ordm_ornm}</span>
                                    </td>
                                    <td>
                                        <span class="order-list-text">${data[i].order_status}</span>
                                    </td>
                                    <td>
                                        <span class="order-list-text">${data[i].ordm_amnt}</span>
                                    </td>
                                    <td style="text-align: center">
                                        <button type="button" class="fa fa-plus-circle text-success collapsed" data-toggle="collapse"
                                            data-target="#order-details-info-${data[i].id}" aria-expanded="false" style="
                                            border: none;
                                            background: none;
                                        "></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6">
                                        ${order_details_data}
                                    </td>
                                <tr>`

                            }

                            $('#order-list').html(order_items)

                        }
                    }
                });
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please Select a Route First !!!',
                });
            }
        }

        function getOrderDetails(order_id = ''){
            let con = $('#con').val()

            if(con != '' && order_id != '') {
                $.ajax({
                    type: "POST",
                    url: "{{URL::to('/')}}/getOrderDetails",
                    data: {
                        order_id: order_id,
                        con: con,
                        _token: token,
                    },
                    cache: "false",
                    success: function (data) {
                        let order_details_data = ''
                        $('.order-details-table').show()
                        $('#order-details-list').empty()


                        if (data && data.length > 0) {

                            for (let i = 0; i < data.length; i++) {
                                order_details_data += `
                                <tr>
                                    <td>
                                        <span class="order-list-text">${data[i].amim_code}</span>
                                    </td>
                                    <td>
                                        <span class="order-list-text">${data[i].amim_name}</span>
                                    </td>
                                    <td>
                                        <span class="order-list-text">${data[i].ordd_inty}</span>
                                    </td>
                                    <td style="text-align: end;">
                                        <span class="order-list-text">${data[i].ordd_oamt}</span>
                                    </td>
                                </tr>`
                            }

                            $('#order-details-list').html(order_details_data)

                        }

                    }, error: (error) => {
                        console.log(error)
                    }
                })
            }else{
                $('#order-details-list').empty()
            }
        }

        function getSubCategories(site_id, country_id = null, slgp_id = null, sr_id = null, dlrm_id = null){
            if(country_id === null) {
                country_id = $('#country_id').val()
            }
            if(slgp_id === null) {
                slgp_id = $('#sales-group-id').val()
            }
            if(sr_id === null) {
                sr_id = $('#employee-id').val()
            }

            if(dlrm_id === null) {
                dlrm_id = $('#dlrm-id').select2('data')[0].id
            }

            if(dlrm_id !== null) {
                $('#np-dlrm-id').val(dlrm_id)
            }

            let info = {
                country_id: country_id,
                slgp_id: slgp_id,
                sr_id: sr_id,
                dlrm_id: dlrm_id,
                _token: token
            }

            if(site_id != ''){
                info = {
                    country_id: country_id,
                    slgp_id: slgp_id,
                    site_id: site_id,
                    dlrm_id: dlrm_id,
                    sr_id: sr_id,
                    _token: token,
                }
            }

            clearInfo()
            $('#cat-id').empty();
            $('#amim-id').empty();
            $('#items-list').empty();


            if(country_id !='' && slgp_id != ''){
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/getSubCategories",
                    data: info,
                    cache:"false",
                    success:function(data){

                        let info = data.categories
                        let country_id = data.country_id

                        $('#country_id').val(country_id)

                        let html='<option value="" selected>Select Category</option>';
                        if(info.length>0){


                            if(country_id === 2 || country_id === 5)
                            {
                                for (var i = 0; i < info.length; i++) {
                                    html += `<option value="${info[i].id}:${info[i].aemp_id}:${info[i].rout_id}:${info[i].slgp_id}:dlrm_id">${info[i].cat_code}-${info[i].cat_name}</option>`;
                                }
                            }else if(country_id === 9){
                                let plmt_id = info[0].plmt_id
                                $('#plmt-id').val(plmt_id)
                                for (var i = 0; i < info.length; i++) {
                                    html += `<option value="${info[i].id}:${sr_id}">${info[i].cat_code}-${info[i].cat_name}</option>`;
                                }
                            }else{
                                let plmt_id = info[0].plmt_id
                                $('#plmt-id').val(plmt_id)
                                for (var i = 0; i < info.length; i++) {
                                    html += `<option value="${info[i].id}">${info[i].cat_code}-${info[i].cat_name}</option>`;
                                }
                            }
                        }

                        $('#cat-id').html(html);
                        if(country_id !== 9) {
                            $('#grv-cat-id').html(html);
                        }
                    }
                });
            }
            else{
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please Select Country, Sales Group and SR First !!!',
                });
            }
        }

        function getItems(){
            let category        = $('#cat-id').select2('data')[0]
            let category_info   = category.id.split(':')
            let country_id      = $('#country_id').val()
            let outlet_id       = $('#outlet-id').val()
            let category_id     = category_info[0]
            let sr_id           = category_info[1]

            let selected_category = $('#cat-id').find("option:selected")
            let dlrm_id;

            // checking category is populated from order edit option
            if(selected_category.is("[order-edit]") && country_id !== '2' && country_id !== '5'){
                dlrm_id = category_info[1];
            }else{
                dlrm_id = $('#dlrm-id').select2('data')[0].id ?? category_info[4]
            }

            if(dlrm_id === ''){
                dlrm_id = $('#np-dlrm-id').val()
            }

            if(outlet_id === ''){
                outlet_id = $('#order-outlets').val()
            }

            $('#amim-id').empty();


            if(country_id !='' && category_id !='' && sr_id !=''){
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/getItems",
                    data:{
                        country_id: country_id,
                        category_id: category_id,
                        sr_id: sr_id,
                        outlet_id: outlet_id,
                        dlrm_id: dlrm_id,
                        _token: token,
                    },
                    cache:"false",
                    success:function(data){

                        let info = data.items
                        let country_id = data.country_id

                        $('#country_id').val(country_id)


                        if(info && info.length>0){
                            $('#amim-id').empty();
                            var html='<option value="" selected>Select an Item</option>';


                            if(country_id === 2 || country_id === 5) {
                                let rout_id     = category_info[2]
                                let slgp_id     = category_info[3]

                                for (var i = 0; i < info.length; i++) {
                                    html += `<option value="${info[i].amim_id}:${info[i].amim_name}:${info[i].amim_code}:${info[i].pldt_tppr}:${info[i].amim_duft}:${sr_id}:${rout_id}:${info[i].plmt_id}:${slgp_id}:${info[i].pldt_tpgp}">${info[i].amim_code}-${info[i].amim_name}</option>`;
                                }
                            }
                            else{
                                for (var i = 0; i < info.length; i++) {
                                    html += `<option value="${info[i].amim_id}:${info[i].amim_name}:${info[i].amim_code}:${info[i].pldt_tppr}:${info[i].amim_duft}:${info[i].pldt_tpgp}">${info[i].amim_code}-${info[i].amim_name}</option>`;
                                }
                            }

                            $('#amim-id').append(html);

                        }
                    }
                });
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please Select Country, Category & SR First !!!',
                });
            }
        }

        function getGrvItems(){
            let category        = $('#grv-cat-id').select2('data')[0]
            let category_info   = category.id.split(':')
            let country_id      = $('#country_id').val()
            let outlet_id       = $('#outlet-id').val()
            let category_id     = category_info[0]
            let sr_id           = category_info[1]

            let selected_category = $('#cat-id').find("option:selected")
            let dlrm_id;

            // checking category is populated from order edit option
            if(selected_category.is("[order-edit]") && country_id !== '2' && country_id !== '5'){
                dlrm_id = category_info[1];
            }else{
                dlrm_id = $('#dlrm-id').select2('data')[0].id ?? category_info[4]
            }

            $('#grv-amim-id').empty();
            if(country_id !='' && category_id !='' && sr_id !=''){
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/getGrvItems",
                    data:{
                        country_id: country_id,
                        category_id: category_id,
                        sr_id: sr_id,
                        outlet_id: outlet_id,
                        dlrm_id: dlrm_id,
                        _token: token,
                    },
                    cache:"false",
                    success:function(data){

                        let info = data.items
                        let country_id = data.country_id

                        $('#country_id').val(country_id)


                        if(info && info.length>0){
                            $('#grv-amim-id').empty();
                            var html='<option value="" selected>Select Grv Item</option>';


                            if(country_id === 2 || country_id === 5) {
                                let rout_id     = category_info[2]
                                let slgp_id     = category_info[3]

                                for (var i = 0; i < info.length; i++) {
                                    html += `<option value="${info[i].amim_id}:${info[i].amim_name}:${info[i].amim_code}:${info[i].pldt_tppr}:${info[i].amim_duft}:${sr_id}:${rout_id}:${info[i].plmt_id}:${slgp_id}:${info[i].pldt_tpgp}">${info[i].amim_code}-${info[i].amim_name}</option>`;
                                }
                            }
                            else{
                                for (var i = 0; i < info.length; i++) {
                                    html += `<option value="${info[i].amim_id}:${info[i].amim_name}:${info[i].amim_code}:${info[i].pldt_tppr}:${info[i].amim_duft}:${info[i].pldt_tpgp}">${info[i].amim_code}-${info[i].amim_name}</option>`;
                                }
                            }

                            $('#grv-amim-id').append(html);

                        }
                    }
                });
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please Select Country, Category & SR First !!!',
                });
            }
        }

        function getOrders(){
            let item_id_exist = true
            $('#item-footer').show()
            let amim = $('#amim-id').select2('data')[0]
            let amim_info = amim.id
            let infos = amim_info.split(':')
            let country_id = $('#country_id').val()
            let outlet_id  = $('#outlet-id').val()

            if(infos.length > 2){

                let amim_id = infos[0]

                $('.added-items-id').each(function () {
                    if ($(this).val() === amim_id) {
                        item_id_exist = false
                    }
                })

                if (item_id_exist) {
                    hasPromotion(infos, country_id, outlet_id)
                }
            }

        }

        function getGrvOrders(){
            let grv_item_exist = true
            $('#grv-item-footer').show()
            let amim = $('#grv-amim-id').select2('data')[0]
            let amim_info = amim.id
            let infos = amim_info.split(':')
            let country_id = $('#country_id').val()
            let outlet_id  = $('#outlet-id').val()


            if(infos.length > 2){

                let amim_id = infos[0]

                let totalOrderItems = $('.added-items-id').length

                if(totalOrderItems !== 0){
                    $('.added-grv-items-id').each(function () {
                        if ($(this).val() === amim_id) {
                            grv_item_exist = false
                        }
                    })

                    if (grv_item_exist) {
                        grvItemsAdd(infos, country_id)
                    }
                }
                else if(totalOrderItems === 0){
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: 'Order at least one item to have GRV',
                    });
                }
            }

        }

        function hasPromotion(infos, country_id, outlet_id){
            let amim_id = infos[0]
            let slgp_id = infos[8]
            let zone = $('#zone-id').select2('data')[0]
            let zone_id = zone.id

            $('#promotion-slgp-id').val(slgp_id)

            if(zone_id === ''){
                zone_id = $('#promotion-zone-id').val()
            }

            if(country_id != '' && outlet_id != '' && amim_id != '') {

                $.ajax({
                    type: "POST",
                    url: "{{URL::to('/')}}/check-global-promotion",
                    data: {
                        amim_id: amim_id,
                        country_id: country_id,
                        site_id: outlet_id,
                        slgp_id: slgp_id,
                        zone_id: zone_id,
                        _token: token
                    },
                    cache: "false",
                    success: function (data) {

                        $("#showPromotion").show();
                        $("#showPromotion").modal({backdrop: false});
                        $('#showPromotion').modal('show');


                        if(data.prom_type === 'FOC') {

                            if (data.slub.length > 0) {
                                let slub_info = data.slub;

                                $('#promotion-heading').text(`Choose ${data.prom_type} Promotion Items`)

                                $('#slub-details').html(`<tr class="tbl_header_light">
                                                        <th class="text-center" style="width: 50% !important;">Order ${data.prom_on === 'QTY' ? 'Qty' : 'Cartoon'}</th>
                                                        <th class="text-center" style="width: 50% !important;">Free ${data.prom_on === 'QTY' ? 'Qty' : 'Cartoon'}</th>
                                                    </tr>`)

                                $('#promotion-details').html(`<tr class="tbl_header_light">
                                                        <th style="width: 65% !important; text-align: left">Item</th>
                                                        <th style="width: 15% !important;">Pcs</th>
                                                        <th style="width: 15% !important;">Ctn</th>
                                                        <th style="width: 5% !important;">Action</th>
                                                    </tr>`)

                                let slub_infos = ''
                                for (let i = 0; i < slub_info.length; i++) {
                                    slub_infos += `<tr>
                                        <td>${slub_info[i]['prsb_fqty']} </td>
                                        <td>${slub_info[i]['prsb_qnty']} </td>
                                    </tr>`;
                                }
                                $('#slub-info').html(slub_infos);
                            }

                            if (data.items.length > 0) {

                                let promotion_info = data.items;

                                let promotion_items = ''
                                for (let i = 0; i < promotion_info.length; i++) {


                                    promotion_items += `<tr id="item-row-${promotion_info[i].amim_id}" class="promotion-row-${data.prom_id}">
                                <td>
                                    ${promotion_info[i].amim_code}-${promotion_info[i].amim_name}
                                    <input type="hidden" value="${promotion_info[i].amim_id}" class="added-items-id promtion-items-"  id="item-id-${promotion_info[i].amim_id}" name="item_ids[]">
                                    <input type="hidden" value="${promotion_info[i].pldt_tppr}" name="item_unit_prices[]"  id="item-price-${promotion_info[i].amim_id}">
                                    <input type="hidden"  name="item_dufts[]" value="${promotion_info[i].amim_duft}" id="ctn-size-${promotion_info[i].amim_id}">
                                </td>
                                <td>
                                    <input type="hidden" id="total-item-${promotion_info[i].amim_id}" class="promotion-item-${data.prom_id}" name="total_qtys[]">
                                    <input type="number" class="in_tg" id="item-qty-${promotion_info[i].amim_id}" style="width: 90%; text-align: end;" onclick="getPromotionSubTotal('${promotion_info[i].amim_id}')"   onkeyup="getPromotionSubTotal('${promotion_info[i].amim_id}')" name="item_qtys[]">
                                </td>
                                <td>
                                    <input type="hidden" id="total-ctn-${promotion_info[i].amim_id}" class="total-ctn-${data.prom_id}">
                                    <input type="number" class="item-id in_tg" id="item-ctn-${promotion_info[i].amim_id}" style="width: 90%; text-align: end;" onclick="getPromotionSubTotal('${promotion_info[i].amim_id}')"   onkeyup="getPromotionSubTotal('${promotion_info[i].amim_id}')"  name="item_ctns[]">
                                </td>
                                <td style="display: none">
                                    <input type="hidden" value="${data.prom_id}" name="item_prom_ids[${promotion_info[i].amim_id}]">
                                    <input type="hidden" name="item_special_discounts[]" value="0">
                                    <input type="number" readonly class="item-discount in_tg" style="width: 100%; text-align: end;"
                                        value="0" id="item-discount-${promotion_info[i].amim_id}" name="item_discounts[]">
                                </td>
                                <td style="display: none">
                                    <input type="number" readonly class="item-subtotal in_tg"
        style="width: 100%; text-align: end;border: 1px solid #5bf728 !important;" id="item-subtotal-${promotion_info[i].amim_id}" name="item_prices[]">
                                </td>
                                <td style="text-align: center">
                                    <i class="fa fa-times-circle-o text-danger" aria-hidden="true" onclick="deleteRow(this, ${data.prom_id})"></i>
                                </td>
                             </tr>`

                                    $('#promotion-info').html(promotion_items);
                                }
                            }
                            if (data.prom_id !== 0) {
                                $('#foc-promotion-id').val(data.prom_id)
                            }
                        }
                        else if(data.prom_type === 'Discount'){
                            if (data.slub.length > 0) {
                                $('#promotion-heading').text(`Choose ${data.prom_type} Promotion Items`)


                                let slub_info = data.slub;

                                $('#slub-details').html(`<tr class="tbl_header_light">
                                                            <th class="text-center" style="width: 50% !important;">Order ${data.prom_on === 'QTY' ? 'Qty' : 'Cartoon'}</th>
                                                            <th class="text-center" style="width: 50% !important;">Free ${data.gift_on === 'percent' ? 'Percent' : 'Amount'}</th>
                                                        </tr>`)

                                $('#promotion-details').html(`<tr class="tbl_header_light">
                                   <th style="width: 40% !important; text-align: left">Item</th>
                                   <th style="width: 15% !important;">Pcs</th>
                                   <th style="width: 10% !important;">Ctn</th>
                                   <th style="width: 15% !important;">Discount</th>
                                   <th style="width: 15% !important;">Subtotal</th>
                                   <th style="width: 5% !important;">Action</th>
                                </tr>`)

                                let slub_infos = ''
                                for (let i = 0; i < slub_info.length; i++) {

                                    slub_infos += `<tr>
                                        <td >${slub_info[i]['prsb_fqty']} </td>
                                        <td >${slub_info[i]['prsb_qnty']} </td>
                                    </tr>`;
                                }
                                $('#slub-info').html(slub_infos);
                            }
                            if (data.items.length > 0) {

                                let promotion_info = data.items;

                                let promotion_items = ''
                                for (let i = 0; i < promotion_info.length; i++) {

                                    promotion_items += `<tr id="item-row-${promotion_info[i].amim_id}" class="promotion-row-${data.prom_id}">
                                        <td>
                                            ${promotion_info[i].amim_code}-${promotion_info[i].amim_name}
                                            <input type="hidden" value="${promotion_info[i].amim_price}" name="item_unit_prices[]"  id="item-price-${promotion_info[i].amim_id}">
                                            <input type="hidden"  name="item_dufts[]" value="${promotion_info[i].amim_ctn_size}" id="ctn-size-${promotion_info[i].amim_id}">
                                        </td>
                                        <td>
                                            <input type="hidden" value="${promotion_info[i].amim_id}" class="added-items-id"  id="item-id-${promotion_info[i].amim_id}" name="item_ids[]">
                                            <input type="hidden" class="promotion-item-${data.prom_id}" id="total-item-${promotion_info[i].amim_id}" name="total_qtys[]">
                                            <input type="number" class="in_tg" id="item-qty-${promotion_info[i].amim_id}" style="width: 90%; text-align: end;" onclick="getPromotionSubTotal('${promotion_info[i].amim_id}')" onkeyup="getPromotionSubTotal('${promotion_info[i].amim_id}')" name="item_qtys[]">
                                        </td>
                                        <td>
                                            <input type="hidden" id="total-ctn-${promotion_info[i].amim_id}" class="total-ctn-${data.prom_id}">
                                            <input type="number" class="item-id in_tg" id="item-ctn-${promotion_info[i].amim_id}" style="width: 90%; text-align: end;" onclick="getPromotionSubTotal('${promotion_info[i].amim_id}')" onkeyup="getPromotionSubTotal('${promotion_info[i].amim_id}')"  name="item_ctns[]">
                                        </td>
                                        <td>
                                            <input type="hidden" id="item-discount-prom-${promotion_info[i].amim_id}" name="item_prom_ids[${promotion_info[i].amim_id}]">
                                            <input type="hidden" name="item_special_discounts[]" value="0">
                                            <input type="number" readonly class="item-discount prom-item-discount-${data.prom_id} in_tg" style="width: 100%; text-align: end;"
                                                value="0" id="item-discount-${promotion_info[i].amim_id}" name="item_discounts[]">
                                        </td>
                                        <td>
                                            <input type="number" readonly class="item-subtotal in_tg item-subtotal-${data.prom_id}"
        style="width: 100%; text-align: end;border: 1px solid #5bf728 !important;" id="item-subtotal-${promotion_info[i].amim_id}" name="item_prices[]">
                                        </td>
                                        <td style="text-align: center">
                                            <i class="fa fa-times-circle-o text-danger" aria-hidden="true" onclick="deleteRow(this, ${data.prom_id})"></i>
                                        </td>
                                     </tr>`

                                    $('#promotion-info').html(promotion_items);
                                }
                            }
                            if (data.prom_id !== 0) {
                                $('#foc-promotion-id').val(data.prom_id)
                            }
                        }
                        else if(data.prom_type === 'Value'){
                            if (data.slub.length > 0) {
                                $('#promotion-heading').text(`Choose ${data.prom_type} Promotion Items`)


                                let slub_info = data.slub;

                                $('#slub-details').html(`<tr class="tbl_header_light">
                                                            <th class="text-center" style="width: 50% !important;">Order Amount</th>
                                                            <th class="text-center" style="width: 50% !important;">Free ${data.gift_on === 'percent' ? 'Percent' : 'Amount'}</th>
                                                        </tr>`)

                                $('#promotion-details').html(`<tr class="tbl_header_light">
                                   <th style="width: 40% !important; text-align: left">Item</th>
                                   <th style="width: 15% !important;">Pcs</th>
                                   <th style="width: 10% !important;">Ctn</th>
                                   <th style="width: 15% !important;">Discount</th>
                                   <th style="width: 15% !important;">Subtotal</th>
                                   <th style="width: 5% !important;">Action</th>
                                </tr>`)

                                let slub_infos = ''
                                for (let i = 0; i < slub_info.length; i++) {

                                    slub_infos += `<tr>
                                        <td >${slub_info[i]['prsb_famn']} </td>
                                        <td >${slub_info[i]['prsb_disc']} </td>
                                    </tr>`;
                                }
                                $('#slub-info').html(slub_infos);
                            }
                            if (data.items.length > 0) {

                                let promotion_info = data.items;

                                let promotion_items = ''
                                for (let i = 0; i < promotion_info.length; i++) {

                                    promotion_items += `<tr id="item-row-${promotion_info[i].amim_id}" class="promotion-row-${data.prom_id}">
                                        <td>
                                            ${promotion_info[i].amim_code}-${promotion_info[i].amim_name}
                                            <input type="hidden" value="${promotion_info[i].amim_price}" name="item_unit_prices[]"  id="item-price-${promotion_info[i].amim_id}">
                                            <input type="hidden"  name="item_dufts[]" value="${promotion_info[i].amim_ctn_size}" id="ctn-size-${promotion_info[i].amim_id}">
                                        </td>
                                        <td>
                                            <input type="hidden" value="${promotion_info[i].amim_id}" class="added-items-id"  id="item-id-${promotion_info[i].amim_id}" name="item_ids[]">
                                            <input type="hidden" class="promotion-item-${data.prom_id}" id="total-item-${promotion_info[i].amim_id}" name="total_qtys[]">
                                            <input type="number" class="in_tg" id="item-qty-${promotion_info[i].amim_id}" style="width: 90%; text-align: end;" onclick="getPromotionSubTotal('${promotion_info[i].amim_id}')"   onkeyup="getPromotionSubTotal('${promotion_info[i].amim_id}')" name="item_qtys[]">
                                        </td>
                                        <td>
                                            <input type="hidden" id="total-ctn-${promotion_info[i].amim_id}" class="total-ctn-${data.prom_id}">
                                            <input type="number" class="item-id in_tg" id="item-ctn-${promotion_info[i].amim_id}" style="width: 90%; text-align: end;" onclick="getPromotionSubTotal('${promotion_info[i].amim_id}')"   onkeyup="getPromotionSubTotal('${promotion_info[i].amim_id}')"  name="item_ctns[]">
                                        </td>
                                        <td>
                                            <input type="hidden" name="item_special_discounts[]" value="0">
                                            <input type="hidden" id="item-discount-prom-${promotion_info[i].amim_id}" name="item_prom_ids[${promotion_info[i].amim_id}]">
                                            <input type="number" readonly class="item-discount prom-item-discount-${data.prom_id} in_tg" style="width: 100%; text-align: end;"
                                                value="0" id="item-discount-${promotion_info[i].amim_id}" name="item_discounts[]">
                                        </td>
                                        <td>
                                            <input type="number" readonly class="item-subtotal in_tg item-subtotal-${data.prom_id}"
        style="width: 100%; text-align: end;border: 1px solid #5bf728 !important;" id="item-subtotal-${promotion_info[i].amim_id}" name="item_prices[]">
                                        </td>
                                        <td style="text-align: center">
                                            <i class="fa fa-times-circle-o text-danger" aria-hidden="true" onclick="deleteRow(this, ${data.prom_id})"></i>
                                        </td>
                                     </tr>`

                                    $('#promotion-info').html(promotion_items);
                                }
                            }
                            if (data.prom_id !== 0) {
                                $('#foc-promotion-id').val(data.prom_id)
                            }
                        }
                        else if(data.prom_type === 'Promotion'){

                            let slub_info = data.slub;

                            $('#promotion-heading').text(`Promotion Information`)

                            $('#slub-details').html(`
                                <tr class="tbl_header_light">
                                    <th class="text-center" style="width: 20% !important;">Order Qty</th>
                                    <th class="text-center" style="width: 60% !important;">Free Qty</th>
                                    <th class="text-center" style="width: 20% !important;">Free Amount</th>
                                </tr>`)

                            let amim_name       = infos[1]
                            let amim_code       = infos[2]

                            $('#promotion-details').html(`<tr class="tbl_header_light">
                               <th style="width: 40% !important; text-align: left">Item</th>
                               <th style="width: 15% !important;">Pcs</th>
                               <th style="width: 10% !important;">Ctn</th>
                               <th style="width: 15% !important;">Discount</th>
                               <th style="width: 20% !important;">Subtotal</th>
                            </tr>`)

                            let slub_infos = ''

                            let free_item_info = data.free_item

                            let free_item = data.different_free_item ? `${free_item_info.amim_code} - ${free_item_info.amim_name}` : `${amim_code} - ${amim_name}`

                                // <td> More Than ${slub_info['prdt_mnbt']}</td>

                            slub_infos = `
                                <tr><td> More Than ${slub_info['prdt_mnbt']} </td>
                                    <td></td>
                                    <td> ${slub_info['prdt_fipr']*(slub_info['prdt_disc']/100)} </td>
                                </tr>
                                <tr>
                                    <td> More Than ${slub_info['prdt_mbqt']} </td>
                                    <td><span style="text-align: center"> ${free_item} (<span style="color: #0b93d5">${slub_info['prdt_fiqt']} Pcs</span>)</span> <span style="float: right;color: #0b93d5;"> OR </span></td>
                                    <td> ${slub_info['prdt_fipr']} </td>
                                </tr>`

                            $('#slub-info').html(slub_infos);

                            let amim_price      = infos[3]
                            let amim_ctn_size   = infos[4]
                            let sr_id           = infos[5]
                            let rout_id         = infos[6]
                            let plmt_id         = infos[7]
                            let slgp_id         = infos[8]

                            let promotion_items =  `<tr id="item-row-${amim_id}" class="promotion-row-${data.prom_id}">
                                <td>
                                    ${amim_code}-${amim_name}
                                    <input type="hidden" value="${amim_id}" class="added-items-id" id="item-id-${amim_id}" name="item_ids[]">
                                    <input type="hidden" value="${amim_price}" name="item_unit_prices[]" id="item-price-${amim_id}">
                                    <input type="hidden" value="${sr_id}" name="item_sr_ids[]" id="item-sr-${amim_id}">
                                    <input type="hidden" value="${rout_id}" name="item_rout_ids[]" id="item-rout-${amim_id}">
                                    <input type="hidden" value="${plmt_id}" name="item_plmt_ids[]" id="item-plmt-${amim_id}">
                                    <input type="hidden" value="${slgp_id}" name="item_slgp_ids[]" id="item-slgp-${amim_id}">
                                    <input type="hidden" value="${amim_ctn_size}" name="item_dufts[]" id="ctn-size-${amim_id}">
                                </td>
                                <td>
                                    <input type="hidden" id="total-item-${amim_id}" class="promotion-item-${data.prom_id}"  name="total_qtys[]">
                                    <input type="number" class="in_tg" id="item-qty-${amim_id}" style="width: 90%; text-align: end;" onclick="getPromotionSubTotal('${amim_id}')" onkeyup="getPromotionSubTotal('${amim_id}')" name="item_qtys[]">
                                </td>
                                <td>
                                    <input type="hidden" id="total-ctn-${amim_id}">
                                    <input type="number" class="item-id in_tg" id="item-ctn-${amim_id}" style="width: 90%; text-align: end;" onclick="getPromotionSubTotal('${amim_id}')" onkeyup="getPromotionSubTotal('${amim_id}')"  name="item_ctns[]">
                                </td>
                                <td>
                                    <input type="hidden" name="item_special_discounts[]" value="0">
                                    <input type="number" readonly class="item-discount in_tg" style="width: 100%; text-align: end;" value="0" id="item-discount-${amim_id}" name="item_discounts[]">
                                </td>
                                <td>
                                    <input type="hidden" value="0" id="item-discount-prom-${amim_id}" name="item_prom_ids[${amim_id}]">
                                    <input type="number" readonly class="item-subtotal in_tg" style="width: 100%; text-align: end;border: 1px solid #5bf728 !important;" id="item-subtotal-${amim_id}" name="item_prices[]">
                                </td>
                                <td style="text-align: center" style="display: none">
                                    <i class="fa fa-times-circle-o text-danger" aria-hidden="true" onclick="deleteRow(this, ${data.prom_id})"></i>
                                </td>
                            </tr>`


                            $('#promotion-info').html(promotion_items);

                            if (data.prom_id !== 0) {
                                $('#foc-promotion-id').val(data.prom_id)
                            }
                        }

                    },
                    error: function (error) {

                        let items = '';
                        let amim_name = infos[1]
                        let amim_code = infos[2]
                        let amim_price = infos[3]
                        let amim_ctn_size = infos[4]

                        let item_lists = $('#items-list')


                        if (country_id === '2' || country_id === '5') {
                            let sr_id = infos[5]
                            let rout_id = infos[6]
                            let plmt_id = infos[7]
                            let slgp_id = infos[8]
                            items = `<tr id="item-row-${amim_id}">
                            <td>
                                ${amim_code}-${amim_name}
                                <input type="hidden" value="${amim_id}" class="added-items-id"  id="item-id-${amim_id}" name="item_ids[]">
                                <input type="hidden" value="${amim_price}" name="item_unit_prices[]"  id="item-price-${amim_id}">
                                <input type="hidden" value="${sr_id}" name="item_sr_ids[]"  id="item-sr-${amim_id}">
                                <input type="hidden" value="${rout_id}" name="item_rout_ids[]"  id="item-rout-${amim_id}">
                                <input type="hidden" value="${plmt_id}" name="item_plmt_ids[]"  id="item-plmt-${amim_id}">
                                <input type="hidden" value="${slgp_id}" name="item_slgp_ids[]"  id="item-slgp-${amim_id}">
                                <input type="hidden"  name="item_dufts[]" value="${amim_ctn_size}" id="ctn-size-${amim_id}">
                            </td>
                            <td>
                                <input type="hidden" id="total-item-${amim_id}" name="total_qtys[]">
                                <input type="number" class="in_tg" id="item-qty-${amim_id}" style="width: 90%; text-align: end;" onclick="getSubTotal('${amim_id}')"   onkeyup="getSubTotal('${amim_id}')" name="item_qtys[]">
                            </td>
                            <td>
                                <input type="hidden" id="total-ctn-${amim_id}">
                                <input type="number" class="item-id in_tg" id="item-ctn-${amim_id}" style="width: 90%; text-align: end;" onclick="getSubTotal('${amim_id}')"   onkeyup="getSubTotal('${amim_id}')"  name="item_ctns[]">
                            </td>
                            <td>
                                <input type="hidden" id="special-disc-info-${amim_id}">
                                <input type="hidden" name="item_discounts[]" value="0">
                                <input type="number" class="item-discount special-discount in_tg"
                                    readonly onclick="showSpecialDiscount('${amim_id}')"
                                    style="width: 100%; text-align: end; border: 1px solid blue !important;" value="0" id="item-discount-${amim_id}"
                                    name="item_special_discounts[]">
                            </td>
                            <td>
                                <input type="number" readonly class="item-subtotal in_tg" style="width: 100%; text-align: end;border: 1px solid #5bf728 !important;"  id="item-subtotal-${amim_id}" name="item_prices[]">
                            </td>
                            <td style="text-align: center">
                                <i class="fa fa-times-circle-o text-danger" aria-hidden="true" onclick="deleteRow(this, ${amim_id})"></i>
                            </td>
                     </tr>`
                        }
                        else {
                            items = `<tr id="item-row-${amim_id}">
                                <td>
                                    ${amim_code}-${amim_name}
                                    <input type="hidden" value="${amim_id}" class="added-items-id"  id="item-id-${amim_id}" name="item_ids[]">
                                    <input type="hidden" value="${amim_price}" name="item_unit_prices[]"  id="item-price-${amim_id}">
                                    <input type="hidden"  name="item_dufts[]" value="${amim_ctn_size}" id="ctn-size-${amim_id}">
                                </td>
                                <td>
                                    <input type="hidden" id="total-item-${amim_id}" name="total_qtys[]">
                                    <input type="number" class="in_tg" id="item-qty-${amim_id}" style="width: 90%; text-align: end;" onclick="getSubTotal('${amim_id}')"   onkeyup="getSubTotal('${amim_id}')" name="item_qtys[]">
                                </td>
                                <td>
                                    <input type="number" class="item-id in_tg" id="item-ctn-${amim_id}" style="width: 90%; text-align: end;" onclick="getSubTotal('${amim_id}')"   onkeyup="getSubTotal('${amim_id}')"  name="item_ctns[]">
                                </td>
                                <td>
                                    <input type="hidden" id="special-disc-info-${amim_id}">
                                    <input type="hidden" value="0" id="item-discount-prom-${amim_id}" name="item_prom_ids[${amim_id}]">
                                    <input type="hidden" name="item_discounts[]" value="0">
                                    <input type="number" class="item-discount special-discount in_tg"
                                        readonly onclick="showSpecialDiscount('${amim_id}')"
                                        style="width: 100%; text-align: end; border: 1px solid blue !important;" value="0" id="item-discount-${amim_id}"
                                        name="item_special_discounts[]">
                                </td>
                                <td>
                                    <input type="number" readonly class="item-subtotal in_tg" style="width: 100%; text-align: end;border: 1px solid #5bf728 !important;" id="item-subtotal-${amim_id}" name="item_prices[]">
                                </td>
                                <td style="text-align: center">
                                    <i class="fa fa-times-circle-o text-danger" aria-hidden="true" onclick="deleteRow(this, ${amim_id})"></i>
                                </td>
                            </tr>`
                        }

                        item_lists.append(items)
                    }
                });
            }
            else{

                let items = '';
                let amim_name = infos[1]
                let amim_code = infos[2]
                let amim_price = infos[3]
                let amim_ctn_size = infos[4]

                let item_lists = $('#items-list')


                if (country_id === '2' || country_id === '5') {
                    let sr_id = infos[5]
                    let rout_id = infos[6]
                    let plmt_id = infos[7]
                    let slgp_id = infos[8]
                    items = `<tr id="item-row-${amim_id}">
                            <td>
                                ${amim_code}-${amim_name}
                                <input type="hidden" value="${amim_id}" class="added-items-id"  id="item-id-${amim_id}" name="item_ids[]">
                                <input type="hidden" value="${amim_price}" name="item_unit_prices[]"  id="item-price-${amim_id}">
                                <input type="hidden" value="${sr_id}" name="item_sr_ids[]"  id="item-sr-${amim_id}">
                                <input type="hidden" value="${rout_id}" name="item_rout_ids[]"  id="item-rout-${amim_id}">
                                <input type="hidden" value="${plmt_id}" name="item_plmt_ids[]"  id="item-plmt-${amim_id}">
                                <input type="hidden" value="${slgp_id}" name="item_slgp_ids[]"  id="item-slgp-${amim_id}">
                                <input type="hidden"  name="item_dufts[]" value="${amim_ctn_size}" id="ctn-size-${amim_id}">
                            </td>
                            <td>
                                <input type="hidden" id="total-item-${amim_id}" name="total_qtys[]">
                                <input type="number" class="in_tg" id="item-qty-${amim_id}" style="width: 90%; text-align: end;" onclick="getSubTotal('${amim_id}')"   onkeyup="getSubTotal('${amim_id}')" name="item_qtys[]">
                            </td>
                            <td>
                                <input type="hidden" id="total-ctn-${amim_id}">
                                <input type="number" class="item-id in_tg" id="item-ctn-${amim_id}" style="width: 90%; text-align: end;" onclick="getSubTotal('${amim_id}')"   onkeyup="getSubTotal('${amim_id}')"  name="item_ctns[]">
                            </td>
                            <td>
                                <input type="hidden" id="special-disc-info-${amim_id}">
                                <input type="hidden" name="item_discounts[]" value="0">
                                <input type="number" class="item-discount special-discount in_tg"
                                    readonly onclick="showSpecialDiscount('${amim_id}')"
                                    style="width: 100%; text-align: end; border: 1px solid blue !important;" value="0"
                                    id="item-discount-${amim_id}" name="item_special_discounts[]">
                            </td>
                            <td>
                                <input type="number" readonly class="item-subtotal in_tg" style="width: 100%; text-align: end;border: 1px solid #5bf728 !important;" id="item-subtotal-${amim_id}" name="item_prices[]">
                            </td>
                            <td style="text-align: center">
                                <i class="fa fa-times-circle-o text-danger" aria-hidden="true" onclick="deleteRow(this, ${amim_id})"></i>
                            </td>
                     </tr>`
                }
                else {
                    items = `<tr id="item-row-${amim_id}">
                                <td>
                                    ${amim_code}-${amim_name}
                                    <input type="hidden" value="${amim_price}" name="item_unit_prices[]"  id="item-price-${amim_id}">
                                    <input type="hidden"  name="item_dufts[]" value="${amim_ctn_size}" id="ctn-size-${amim_id}">
                                </td>
                                <td>
                                    <input type="hidden" value="${amim_id}" class="added-items-id"  id="item-id-${amim_id}" name="item_ids[]">
                                    <input type="hidden" id="total-item-${amim_id}" name="total_qtys[]">
                                    <input type="number" class="in_tg" id="item-qty-${amim_id}" style="width: 90%; text-align: end;" onclick="getSubTotal('${amim_id}')"   onkeyup="getSubTotal('${amim_id}')" name="item_qtys[]">
                                </td>
                                <td>
                                    <input type="hidden" id="total-ctn-${amim_id}">
                                    <input type="number" class="item-id in_tg" id="item-ctn-${amim_id}" style="width: 90%; text-align: end;" onclick="getSubTotal('${amim_id}')"   onkeyup="getSubTotal('${amim_id}')"  name="item_ctns[]">
                                </td>
                                <td>
                                    <input type="hidden" id="item-discount-prom-${amim_id}" name="item_prom_ids[]">
                                    <input type="hidden" id="special-disc-info-${amim_id}">

                                <input type="hidden" name="item_discounts[]" value="0">
                                <input type="number" class="item-discount special-discount in_tg"
                                    readonly onclick="showSpecialDiscount('${amim_id}')"
                                    style="width: 100%; text-align: end; border: 1px solid blue !important;" value="0" id="item-discount-${amim_id}"
                                    name="item_special_discounts[]">
                                </td>
                                <td>
                                    <input type="number" readonly class="item-subtotal in_tg" style="width: 100%; text-align: end;border: 1px solid #5bf728 !important;" id="item-subtotal-${amim_id}" name="item_prices[]">
                                </td>
                                <td style="text-align: center">
                                    <i class="fa fa-times-circle-o text-danger" aria-hidden="true" onclick="deleteRow(this, ${amim_id})"></i>
                                </td>
                             </tr>`
                }

                item_lists.append(items)
            }
        }

        function getPromotionCalculation(){
            let country_id  = $('#country_id').val()
            let prom_id     = $('#foc-promotion-id').val()
            let slgp_id     = $('#promotion-slgp-id').val()
            let order_qty   = 0
            let order_amnt  = 0
            let order_ctn   = 0

            $(`.item-subtotal-${prom_id}`).each(function (){
                order_amnt+=Number($(this).val())
            })

            $(`.promotion-item-${prom_id}`).each(function (){
                order_qty+=Number($(this).val())
            })

            $(`.total-ctn-${prom_id}`).each(function (){
                order_ctn+=Number($(this).val())
            })

            let outlet_id;

            if(outlets.length > 0) {
                outlet_id = outlets[0].id;
            }else{
                outlet_id = $('#outlet-id').val();
            }


            if(order_qty > 0) {
                $.ajax({
                    type: "POST",
                    url: "{{URL::to('/')}}/calculate-global-promotion",
                    data: {
                        country_id: country_id,
                        prom_id: prom_id,
                        order_qty: order_qty,
                        order_ctn: order_ctn.toFixed(2),
                        order_amnt: order_amnt.toFixed(2),
                        site_id: outlet_id,
                        slgp_id: slgp_id,
                        _token: token,
                    },
                    cache: "false",
                    success: function (data) {
                        if(data.type == 'promotion'){
                            if(data.free_qty > 0){
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Free Item or Amount',
                                    showCancelButton: true,
                                    confirmButtonText: 'Item',
                                    cancelButtonText: 'Amount',

                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        let free_item = data.item

                                        let sr_id = $(`#item-sr-${data.promotion_item}`).val()

                                        let free_item_info = `
                                            <tr id="free-item-${data.prom_id}" class="promotion-row-${data.prom_id}" style="color: #0036fb;">
                                            <td>
                                                ${free_item.amim_code}-${free_item.amim_name}

                                                <input type="hidden" value="${data.prom_id}" class="added-items-prom-id" name="free_item_prom_ids[${sr_id}]">
                                                <input type="hidden" value="${free_item.pldt_tppr}" name="free_item_unit_prices[${sr_id}]">
                                                <input type="hidden" value="${free_item.amim_duft}" name="free_item_dufts[${sr_id}]">
                                                <input type="hidden" value="${free_item.id}" id="free-item-id-${free_item.id}" name="free_item_ids[${sr_id}]">
                                            </td>
                                            <td>
                                                <input type="number" value="${data.free_qty}" name="free_item_qtys[${sr_id}]" readonly
                                                        class="in_tg" id="free-item-qty-${free_item.id}" style="width: 90%; text-align: end;">
                                            </td>
                                        </tr>`



                                        $(`#item-discount-prom-${data.promotion_item}`).val(data.prom_id)
                                        $(`#free-item-${data.prom_id}`).remove()
                                        $(`#promotion-info`).append(free_item_info)


                                        if(data.free_amount > 0){
                                            item_discounts[data.promotion_item] = data.free_amount
                                            $(`#item-discount-${data.promotion_item}`).val(data.free_amount)
                                        }else{
                                            item_discounts = []
                                            $(`#item-discount-${data.promotion_item}`).val(0)
                                        }
                                    }
                                    else{
                                        let free_item = data.item
                                        $(`#item-discount-prom-${data.promotion_item}`).val(data.prom_id)

                                        $(`#free-item-${data.prom_id}`).remove()
                                        if(data.free_qty > 0){
                                            let free_amount = ((free_item.pldt_tppr * order_qty)/(order_qty+data.free_qty))*data.free_qty + data.free_amount
                                            item_discounts[data.promotion_item] = free_amount.toFixed(2)
                                            $(`#item-discount-${data.promotion_item}`).val(free_amount.toFixed(2))
                                        }
                                    }
                                })
                                $('#order-qty').val(order_qty)
                                $('#prom-type').val(data.type)
                            }
                            else if(data.free_amount > 0){
                                $(`#free-item-${data.prom_id}`).remove()
                                $(`#item-discount-${data.promotion_item}`).val(data.free_amount)
                                $('#order-qty').val(order_qty)
                                $('#prom-type').val(data.type)
                                $(`#item-discount-prom-${data.promotion_item}`).val(data.prom_id)
                                item_discounts[data.promotion_item] = data.free_amount.toFixed(2)
                            }
                            else{
                                item_discounts = []
                                $(`#item-discount-prom-${data.promotion_item}`).val(0)
                                $(`#item-discount-${data.promotion_item}`).val(0)
                                $(`#free-item-${data.prom_id}`).remove()
                                $('#order-qty').val(order_qty)
                                $('#prom-type').val(data.type)
                            }
                        }
                        else if (data.free_qty > 0) {
                            let promotion_item = data.item_info

                            let free_item_info = ''

                            if (promotion_item.length > 0) {
                                for (let i = 0; i < promotion_item.length; i++) {
                                    free_item_info += `
                                        <tr id="free-item-${data.prom_id}" class="promotion-row-${data.prom_id}" style="color: #0036fb;">
                                            <td>
                                                ${promotion_item[i].amim_code}-${promotion_item[i].amim_name}
                                                <input type="hidden" value="${promotion_item[i].id}" id="free-item-id-${promotion_item[i].id}" name="free_item_ids[]">
                                            </td>
                                            <td>
                                                <input type="hidden" value="${data.prom_id}" name="free_item_prom_ids[]">
                                                <input type="number" value="${data.free_qty}" name="free_item_qtys[]" readonly
                                                        class="in_tg" id="free-item-qty-${promotion_item[i].id}" style="width: 90%; text-align: end;">
                                            </td>
                                        </tr>`
                                }
                            }
                            else {
                                let free_item_qty = (data.qfon === 'QTY') ?
                                    `<td>
                                        <input type="hidden" value="${data.prom_id}" name="free_item_prom_ids[]">
                                        <input type="number" value="${data.free_qty}" name="free_item_qtys[]" readonly
                                                   class="in_tg" id="free-item-qty-${promotion_item.id}" style="width: 90%; text-align: end;">
                                    </td><td></td>` :
                                    `<td></td><td>
                                        <input type="hidden" value="${data.free_qty*promotion_item.amim_duft}" name="free_item_qtys[]" readonly
                                                class="in_tg" id="free-item-qty-${promotion_item.id}" style="width: 90%; text-align: end;">
                                        <input type="number" value="${data.free_qty}" readonly
                                                class="in_tg" style="width: 90%; text-align: end;">
                                    </td>`

                                free_item_info = `
                                    <tr id="free-item-${data.prom_id}" class="promotion-row-${data.prom_id}" style="color: #0036fb;">
                                        <td>
                                            ${promotion_item.amim_code}-${promotion_item.amim_name}

                                            <input type="hidden" value="${promotion_item.id}" class="added-items-id" name="free_item_ids[]">
                                            <input type="hidden" value="${data.prom_id}" class="added-items-prom-id" name="free_item_prom_ids[]">
                                            <input type="hidden" value="${promotion_item.pldt_tppr}" name="free_item_unit_prices[]">
                                            <input type="hidden" value="${promotion_item.amim_duft}" name="free_item_dufts[]">
                                        </td>
                                        ${free_item_qty}
                                   </tr>`
                            }

                            $(`#free-item-${data.prom_id}`).remove()
                            $(`#promotion-info`).append(free_item_info)

                            $('#order-qty').val(order_qty)
                            $('#prom-type').val(data.type)
                        }
                        else if(Number(data.discount) > 0 && data.qfon === 'QTY'){
                            $(`.promotion-item-${data.prom_id}`).each(function() {
                                let discount            = Number(data.discount)
                                let item                = $(this).attr('id')
                                let item_id             = item.split('-')[2]
                                let qty                 = $(this).val()
                                let discount_portion    = discount*(qty/order_qty)

                                item_discounts[item_id] = discount_portion.toFixed(2)
                                $(`#item-discount-${item_id}`).val(discount_portion.toFixed(2))
                                $(`#item-discount-prom-${item_id}`).val(data.prom_id)
                            })
                            $('#order-qty').val(order_qty)
                            $('#prom-type').val(data.type)
                        }
                        else if(Number(data.discount) > 0 && data.qfon === 'CRT'){
                            $(`.total-ctn-${data.prom_id}`).each(function() {
                                let discount            = Number(data.discount)
                                let item                = $(this).attr('id')
                                let item_id             = item.split('-')[2]
                                let ctn                 = $(this).val()
                                let discount_portion    = discount*(ctn/order_ctn)

                                item_discounts[item_id] = discount_portion.toFixed(2)
                                $(`#item-discount-${item_id}`).val(discount_portion.toFixed(2))
                                $(`#item-discount-prom-${item_id}`).val(data.prom_id)
                            })
                            $('#order-qty').val(order_qty)
                            $('#prom-type').val(data.type)
                        }
                        else if(Number(data.discount) === 0){
                            $(`.promotion-item-${data.prom_id}`).each(function() {
                                let item                = $(this).attr('id')
                                let item_id             = item.split('-')[2]

                                item_discounts = []
                                $(`#item-discount-${item_id}`).val(0)
                                $(`#item-discount-prom-${item_id}`).removeAttr('value')
                            })
                            $('#order-qty').val(order_qty)
                            $('#prom-type').val(data.type)
                        }
                        else if(data.value > 0){
                            $(`.item-subtotal-${data.prom_id}`).each(function() {
                                let amount              = Number(data.value)
                                let item                = $(this).attr('id')
                                let item_id             = item.split('-')[2]
                                let subtotal            = $(this).val()
                                let discount_portion    = amount*(subtotal/order_amnt)

                                item_discounts[item_id] = discount_portion.toFixed(2)
                                $(`#item-discount-${item_id}`).val(discount_portion.toFixed(2))
                                $(`#item-discount-prom-${item_id}`).val(data.prom_id)
                            })
                            $('#order-qty').val(order_qty)
                            $('#prom-type').val(data.type)
                        }
                        else if(data.value === 0){
                            $(`.promotion-item-${data.prom_id}`).each(function() {
                                let item                = $(this).attr('id')
                                let item_id             = item.split('-')[2]

                                item_discounts = []
                                $(`#item-discount-${item_id}`).val(0)
                                $(`#item-discount-prom-${item_id}`).removeAttr('value')
                            })
                            $('#order-qty').val(order_qty)
                            $('#prom-type').val(data.type)
                        }
                        else if(data.free_qty === 0){
                            $(`#free-item-${prom_id}`).remove()
                            $('#order-qty').val(order_qty)
                            $('#prom-type').val(data.type)
                        }
                        else{
                            item_discounts = []
                            $(`#item-discount-prom-${data.promotion_item}`).val(0)
                            $(`#item-discount-${data.promotion_item}`).val(0)
                            $(`#free-item-${data.prom_id}`).remove()
                            $('#order-qty').val(order_qty)
                            $('#prom-type').val(data.type)
                        }
                    }
                })
            }
            else{
                let item_id_info = $(`.promotion-row-${prom_id}`).attr('id')
                let item_id = item_id_info.split('-')[2] ?? 0
                if(item_id !== 0) {
                    $(`#item-discount-${item_id}`).val(0)
                }
                $(`#free-item-${prom_id}`).remove()
            }
        }

        function getPromotionSubTotal(amim_id){
            let item_price          = Number($(`#item-price-${amim_id}`).val())
            let qty                 = Number($(`#item-qty-${amim_id}`).val())|0
            let ctn_size            = Number($(`#ctn-size-${amim_id}`).val())|0
            let ctn_qty             = Number($(`#item-ctn-${amim_id}`).val())|0
            let total_qty           =  qty+(ctn_size)*ctn_qty
            let total_ctn           =  (qty/ctn_size)+ctn_qty
            let sub_total = (item_price*qty + ctn_size*ctn_qty*item_price).toFixed(2)
            item_qtys[amim_id]      = qty
            item_ctns[amim_id]      = ctn_qty
            item_subtotals[amim_id] = (item_price*qty + ctn_size*ctn_qty*item_price).toFixed(2)
            $(`#item-subtotal-${amim_id}`).val(sub_total)

            $(`#total-item-${amim_id}`).val(total_qty)
            $(`#total-ctn-${amim_id}`).val(total_ctn)
        }

        function getSubTotal(amim_id){
            let item_price = Number($(`#item-price-${amim_id}`).val())
            let qty = Number($(`#item-qty-${amim_id}`).val())|0
            let ctn_size = Number($(`#ctn-size-${amim_id}`).val())|0
            let ctn_qty = Number($(`#item-ctn-${amim_id}`).val())|0
            let total_qty           =  qty+(ctn_size)*ctn_qty
            let sub_total = (item_price*qty + ctn_size*ctn_qty*item_price).toFixed(2)


            $(`#total-item-${amim_id}`).val(total_qty)
            $(`#item-subtotal-${amim_id}`).val(sub_total)

            getTotal()
        }

        function getGrvSubTotal(amim_id){
            let item_price = Number($(`#grv-item-price-${amim_id}`).val())
            let qty = Number($(`#grv-item-qty-${amim_id}`).val())|0
            let ctn_size = Number($(`#grv-ctn-size-${amim_id}`).val())|0
            let ctn_qty = Number($(`#grv-item-ctn-${amim_id}`).val())|0
            let total_qty           =  qty+(ctn_size)*ctn_qty
            let sub_total = (item_price*qty + ctn_size*ctn_qty*item_price).toFixed(2)

            $(`#grv-total-item-${amim_id}`).val(total_qty)
            $(`#grv-item-subtotal-${amim_id}`).val(sub_total)

            getGrvTotal()
        }

        function insertFocIntoOrder(){
            let prom_id     = $('#foc-promotion-id').val()
            let prom_type   = $('#prom-type').val()

            order_qty = Number($('#order-qty').val()|0)

            current_qty = 0
            discount_qty = 0


            $(`.promotion-item-${prom_id}`).each(function (){
                current_qty+=Number($(this).val())
            })


            $(`.prom-item-discount-${prom_id}`).each(function (){
                discount_qty+=Number($(this).val())
            })

            // console.log(prom_type, order_qty, current_qty, typeof order_qty, typeof current_qty);

            if(prom_type === 'foc' && order_qty === current_qty){


                let promotion_items = $("#promotion-info").html()

                let items_with_foc = promotion_items.replace(/getPromotionSubTotal/g, 'getSubTotal')

                let items_foc = items_with_foc.replace(/none/g, 'revert')

                let items_foc_infos = items_foc.replace(/deleteRow/g, 'deletePromotionRows')

                $('#items-list').append(items_foc_infos)

                $('#showPromotion').modal('hide');

                item_qtys.forEach((qty, id) => {
                    $(`#item-qty-${id}`).val(qty)
                    $(`#item-qty-${id}`).attr('readonly', true)
                    $(`#item-qty-${id}`).removeAttr('onkeyup')
                    $(`#item-qty-${id}`).removeAttr('onclick')
                    $(`#item-ctn-${id}`).val(item_ctns[id])
                    $(`#item-ctn-${id}`).attr('readonly', true)
                    $(`#item-ctn-${id}`).removeAttr('onkeyup')
                    $(`#item-ctn-${id}`).removeAttr('onclick')
                    $(`#item-subtotal-${id}`).val(item_subtotals[id])
                })

                resetPromotion()
                // let promotion_info = $(`promotion-info-${data.prom_id}`).val();


            }

            else if(prom_type === 'discount' && order_qty === current_qty){


                let promotion_items = $("#promotion-info").html()

                let items_with_foc = promotion_items.replace(/getPromotionSubTotal/g, 'getSubTotal')

                let items_foc = items_with_foc.replace(/none/g, 'revert')

                let items_foc_infos = items_foc.replace(/deleteRow/g, 'deletePromotionRows')

                $('#items-list').append(items_foc_infos)

                $('#showPromotion').modal('hide');

                item_qtys.forEach((qty, id) => {
                    $(`#item-qty-${id}`).val(qty)
                    $(`#item-qty-${id}`).attr('readonly', true)
                    $(`#item-qty-${id}`).removeAttr('onkeyup')
                    $(`#item-qty-${id}`).removeAttr('onclick')
                    $(`#item-ctn-${id}`).val(item_ctns[id])
                    $(`#item-ctn-${id}`).attr('readonly', true)
                    $(`#item-discount-${id}`).val(item_discounts[id])
                    $(`#item-discount-${id}`).attr('readonly', true)
                    $(`#item-ctn-${id}`).removeAttr('onkeyup')
                    $(`#item-ctn-${id}`).removeAttr('onclick')
                    $(`#item-subtotal-${id}`).val(item_subtotals[id])
                })

                resetPromotion()
                // let promotion_info = $(`promotion-info-${data.prom_id}`).val();


            }

            else if(prom_type === 'value' && order_qty === current_qty){


                let promotion_items = $("#promotion-info").html()

                let items_with_foc = promotion_items.replace(/getPromotionSubTotal/g, 'getSubTotal')

                let items_foc = items_with_foc.replace(/none/g, 'revert')

                let items_foc_infos = items_foc.replace(/deleteRow/g, 'deletePromotionRows')

                $('#items-list').append(items_foc_infos)

                $('#showPromotion').modal('hide');

                item_qtys.forEach((qty, id) => {
                    $(`#item-qty-${id}`).val(qty)
                    $(`#item-qty-${id}`).attr('readonly', true)
                    $(`#item-qty-${id}`).removeAttr('onkeyup')
                    $(`#item-qty-${id}`).removeAttr('onclick')
                    $(`#item-ctn-${id}`).val(item_ctns[id])
                    $(`#item-ctn-${id}`).attr('readonly', true)
                    $(`#item-discount-${id}`).val(item_discounts[id])
                    $(`#item-discount-${id}`).attr('readonly', true)
                    $(`#item-ctn-${id}`).removeAttr('onkeyup')
                    $(`#item-ctn-${id}`).removeAttr('onclick')
                    $(`#item-subtotal-${id}`).val(item_subtotals[id])
                })

                resetPromotion()
                // let promotion_info = $(`promotion-info-${data.prom_id}`).val();


            }

            else if(prom_type === 'promotion' && order_qty === current_qty){

                let promotion_items = $("#promotion-info").html()

                let items_with_foc = promotion_items.replace(/getPromotionSubTotal/g, 'getSubTotal')

                let items_foc = items_with_foc.replace(/none/g, 'revert')

                let items_foc_infos = items_foc.replace(/deleteRow/g, 'deletePromotionRows')

                $('#items-list').append(items_foc_infos)

                $('#showPromotion').modal('hide');

                item_qtys.forEach((qty, id) => {
                    $(`#item-qty-${id}`).val(qty)
                    $(`#item-qty-${id}`).attr('readonly', true)
                    $(`#item-qty-${id}`).removeAttr('onkeyup')
                    $(`#item-qty-${id}`).removeAttr('onclick')
                    $(`#item-ctn-${id}`).val(item_ctns[id])
                    $(`#item-ctn-${id}`).attr('readonly', true)
                    $(`#item-ctn-${id}`).removeAttr('onkeyup')
                    $(`#item-ctn-${id}`).removeAttr('onclick')
                    $(`#item-discount-${id}`).val(item_discounts[id])
                    $(`#item-discount-${id}`).attr('readonly', true)
                    $(`#item-ctn-${id}`).removeAttr('onkeyup')
                    $(`#item-ctn-${id}`).removeAttr('onclick')
                    $(`#item-subtotal-${id}`).val(item_subtotals[id])
                })

                resetPromotion()
            }

            else{
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please Calculate the Promotion',
                });
            }

            getDiscountTotal()
        }

        function resetPromotion(){
            $('#foc-promotion-id').val(0)
            $('#promotion-slgp-id').removeAttr("value")
            $('#order-qty').val(0)
            item_ctns = []
            item_qtys = []
            item_subtotals = []
            item_discounts = []
            $('#promotion-info').empty();
            $('#slub-info').empty();
            $('#promotion-heading').text(`Choose Promotion Items`)
            $('#prom-type').removeAttr('value')

            getTotal()
            getDiscountTotal()
        }

        function getTotal(){
            let total = 0
            let discount = 0
            $('.item-subtotal').each(function (){
                total+=Number($(this).val())
            })
            $('.item-discount').each(function (){
                discount+=Number($(this).val())
            })

            $('#total-price').val((total-discount).toFixed(2))
        }

        function getDiscountTotal(){
            let discount = 0

            $('.item-discount').each(function (){
                discount+=Number($(this).val())
            })

            $('#total-discount').val((discount).toFixed(2))
        }

        function getGrvTotal(){
            let total = 0
            $('.grv-item-subtotal').each(function (){
                total+=Number($(this).val())
            })

            $('#grv-total-price').val((total).toFixed(2))
        }

        function setNonProductiveNote(){
            let npro_type = $('#non-productive-type').select2('data')[0]

            $('#npro-note').val(npro_type.text)
            let selected_npro_type = $('#non-productive-type').val();
            let country_id = $('#country-id').val();


            if(selected_npro_type === '7' && country_id === '14'){
                need_audio = false
                $('#note-type-span').empty()
                $('#recording-file').removeAttr('required')
                $('#recording-file').attr('disabled', true)
            }else{
                need_audio = true
                $('#note-type-span').html('*')
                $('#recording-file').attr('required', true)
                $('#recording-file').removeAttr('disabled')
            }
        }

        function uploadAudio(audio_file){
            /*
            let file = audio_file[0]
            let file_name = file.name

            let extension = file_name.split('.').pop()

            let fileKey = "{{ auth()->user()->country()->cont_imgf.
                '/trn/tel_ord/'.date('Y-m-d').
                '/'.uniqid().
                '.'}}"

            fileKey = fileKey+extension;

*/


                // Set the S3 upload parameters
                // var params = {
                //     Bucket: bucketName,
                //     Key: fileKey,
                //     ContentType: extension,
                //     Body: file,
                //     ACL: 'public-read'
                // };
                //
                // console.log(fileKey);

                // s3.setRequestHeader('Access-Control-Allow-Origin','*')
                // // Upload the file to S3
                // s3.upload(params, function(err, data) {
                //     if (err) {
                //         console.log(err);
                //     } else {
                //         console.log('File uploaded successfully. File URL:', data.Location);
                //     }
                // });



            // }

            // console.log(token);

            // $.ajax({
            let fileInput = document.getElementById('recording-file');
            let file = fileInput.files[0];

            let formData1 = new FormData();
            formData1.append('audio', file);


            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/tele-order-audio', true);
            xhr.onload = () => {
                if (xhr.status === 200) {
                    console.log('Audio file uploaded successfully');
                } else {
                    console.error('Failed to upload audio file');
                }
            };
            xhr.send(formData1);

                // cache: "false",
                // success: function (data) {
                //     console.log(data)
                // },
                // error: function (error) {
                //     console.log(error)
                //     Swal.fire({
                //         icon: 'error',
                //         title: 'Failed',
                //         text: error.responseJSON.error,
                //     });
                // }
            // });
        }

        function storeTeleOrder(){

            // alert('tele order hitted')
            //
            // let audio_file = $('#recording-file')[0].files
            //
            // if(audio_file.length > 0) {
            //     uploadAudio(audio_file[0])
            // }
            //
            //
            // return;

            let hasSpecialDiscount = false

            $('.special-discount').each(function () {
                if(Number($(this).val() > 0)){
                    hasSpecialDiscount = true
                }
            })


            if(hasSpecialDiscount){
                $('#has-special-discount').val(1)
            }else{
                $('#has-special-discount').val(0)
            }


            let order_info = new FormData($('#tele-order').get(0));

            let audio_files = $('#recording-file')[0].files

            let totalGrv = $('.added-grv-items-id').length
            let totalOrderItems = $('.added-items-id').length

            let orderAmount = Number($('#total-price').val())

            let grvTotalAmount = 0

            $('.grv-item-subtotal').each(function (){
                grvTotalAmount+=Number($(this).val())
            })


            let outlet_id =  outlets[0].id


            let country_id = $('#country_id').val();


            if((country_id === '3') && orderAmount > 0 && orderAmount < 30){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: `Order Amount (${orderAmount}) must be more than 30`,
                });
                return;
            }
            else if((country_id === '2' || country_id === '14') && orderAmount > 0 && orderAmount < 48){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: `Order Amount (${orderAmount}) must be more than 48`,
                });
                return;
            }
            else if((country_id !== '2' && country_id !== '3' && country_id !== '14') && orderAmount > 0 && orderAmount < 100){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: `Order Amount (${orderAmount}) must be more than 100`,
                });
                return;
            }
            else if(need_audio && audio_files.length === 0){
                Swal.fire({
                    icon: 'info',
                    title: 'Voice clip is missing, want to continue',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',

                }).then((result) => {
                    if (result.isConfirmed) {

                        if((country_id === '3') && orderAmount > 0 && orderAmount < 30){
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: `Order Amount (${orderAmount}) must be more than 30`,
                            });
                            return;
                        }
                        else if((country_id === '2' || country_id === '14') && orderAmount > 0 && orderAmount < 48){
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: `Order Amount (${orderAmount}) must be more than 48`,
                            });
                            return;
                        }
                        else if((country_id !== '2' && country_id !== '3' && country_id !== '14') && orderAmount > 0 && orderAmount < 100){
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: `Order Amount (${orderAmount}) must be more than 100`,
                            });
                            return
                        }
                        else if(totalGrv > 0 && totalOrderItems > 0 && orderAmount < grvTotalAmount){
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: `GRV Amount (${grvTotalAmount}) can not be more than Order Amount (${orderAmount})`,
                            });

                            return;
                        }
                        else if(totalGrv > 0 && totalOrderItems === 0){
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: 'Order at least one item to have GRV',
                            });
                            return;
                        }

                        $.ajax({
                            type: "POST",
                            url: "{{URL::to('/')}}/tele-order-promotion",
                            data: order_info,
                            processData: false,
                            contentType: false,
                            cache: "false",
                            success: function (data) {

                                let country_id = $('#country_id').val()

                                let sites = data.outlets

                                if (data.hasOwnProperty('visited_outlet_in_rout') && data.visited_outlet_in_rout !== null) {
                                    $('#outlet-serial').html(`${data.visited_outlet_in_rout} out of ${total_outlet}`)
                                }

                                if (sites.length > 0) {
                                    getOutletInfo(sites[0].id)
                                    let outlet_options=`<option value="">Select Outlet</option>
                                        <option value="${sites[0].id}" selected>${sites[0].site_code}-${sites[0].site_name}</option>`;
                                    for(let i=1;i<sites.length;i++){
                                        outlet_options+='<option value="'+sites[i].id+'">'+sites[i].site_code+'-'+sites[i].site_name+'</option>';
                                    }
                                    $('#order-outlet-id').html(outlet_options);
                                }

                                $('#items-list').empty()
                                $('#order-details-list').empty()
                                $('#recording-file').val('')
                                $('#grv-items-list').empty()
                                $('#grv-total-price').val('')
                                $('#has-special-discount').removeAttr("value")
                                resetInvoiceSpecialDiscount()
                                $('#special-discount').val('')
                                $('#special-discount-percent').val('')
                                $('#total-discount').val('')
                                $('#total-price').val(0)


                                if(country_id === '9' && data.hasOwnProperty('order_id') && data.order_id !== null) {
                                    Swal.fire({
                                        icon: 'info',
                                        title: 'Want to share with your manager',
                                        showCancelButton: true,
                                        confirmButtonText: 'Yes',
                                        cancelButtonText: 'No',

                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            let manager_info = data.manager_info

                                            if(data.order_id > 0){
                                                getOrderAsPdf(data.order_id, country_id, manager_info, data.order_no.ordm_ornm)
                                            }
                                        }
                                    })
                                }else {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: 'Order Stored Successfully',
                                    });
                                }

                            },
                            error: function (error) {
                                if (error.responseJSON.hasOwnProperty('exist') && error.responseJSON.exist === 1) {
                                    $('#items-list').empty()

                                    $('#outlet-serial').html(`${error.responseJSON.visited_outlet_in_rout} out of ${total_outlet}`)

                                    outlets.shift()

                                    if (outlets.length > 0) {
                                        getOutletInfo(outlets[0].id)
                                        $('#order-details-list').empty()
                                    }
                                }else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Failed',
                                        text: error.responseJSON.error,
                                    });
                                }
                            }
                        });
                    }
                })
            }
            else if(need_audio && audio_files.length > 0 && audio_files[0].size > 5*1024*1024){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'File size must be less than 5MB',
                });
            }
            else if(need_audio && audio_files.length > 0 && !audio_files[0].type.startsWith("audio")){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'File type must be an audio',
                });
            }
            else if(totalGrv > 0 && totalOrderItems === 0){
                console.log('check grv without order')
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Order at least one item to have GRV',
                });
            }
            else if(totalGrv > 0 && orderAmount < grvTotalAmount){
                console.log('check grv amount less than order')

                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: `GRV Amount (${grvTotalAmount}) can not be more than Order Amount (${orderAmount})`,
                });
            }
            else {
                $.ajax({
                    type: "POST",
                    url: "{{URL::to('/')}}/tele-order-promotion",
                    data: order_info,
                    processData: false,
                    contentType: false,
                    cache: "false",
                    success: function (data) {

                        let country_id = $('#country_id').val()



                        let sites = data.outlets

                        if (data.hasOwnProperty('visited_outlet_in_rout') && data.visited_outlet_in_rout !== null) {
                            $('#outlet-serial').html(`${data.visited_outlet_in_rout} out of ${total_outlet}`)
                        }

                        if (sites.length > 0) {
                            getOutletInfo(sites[0].id)
                            $('#order-outlet-id').empty()
                            let outlet_options=`<option value="">Select Outlet</option>
                                <option value="${sites[0].id}" selected>${sites[0].site_code}-${sites[0].site_name}</option>`;
                            for(let i=1;i<sites.length;i++){
                                outlet_options+='<option value="'+sites[i].id+'">'+sites[i].site_code+'-'+sites[i].site_name+'</option>';
                            }
                            $('#order-outlet-id').html(outlet_options);
                        }

                        $('#items-list').empty()
                        $('#order-details-list').empty()
                        $('#recording-file').val('')
                        $('#grv-items-list').empty()
                        $('#grv-total-price').val('')
                        $('#special-discount').val('')
                        $('#special-discount-percent').val('')
                        $('#total-discount').val('')
                        $('#total-price').val(0)


                        resetInvoiceSpecialDiscount()

                        if(country_id === '9' && data.hasOwnProperty('order_id') && data.order_id !== null) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Want to share with your manager',
                                showCancelButton: true,
                                confirmButtonText: 'Yes',
                                cancelButtonText: 'No',

                            }).then((result) => {
                                if (result.isConfirmed) {
                                    let manager_info = data.manager_info

                                    if(data.order_id > 0){
                                        getOrderAsPdf(data.order_id, country_id, manager_info, data.order_no.ordm_ornm)
                                    }
                                }
                            })

                        }
                        else{
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Order Stored Successfully',
                            });
                        }

                    },
                    error: function (error) {
                        console.log(error.code)
                        if (error.responseJSON.hasOwnProperty('exist') && error.responseJSON.exist === 1) {
                            $('#items-list').empty()

                            $('#outlet-serial').html(`${error.responseJSON.visited_outlet_in_rout} out of ${total_outlet}`)

                            outlets.shift()

                            if (outlets.length > 0) {
                                getOutletInfo(outlets[0].id)
                                $('#order-details-list').empty()
                            }
                        }else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: error.responseJSON.error,
                            });
                        }
                    }
                });
            }
        }

        function getOrderAsPdf(order_id, country_id, manager_info, order_no) {
            if (order_id !== '' && country_id !== '')
            {
                $.ajax({
                    type: "POST",
                    url: "{{URL::to('/')}}/get-order-as-pdf",
                    data: {
                        '_token': token,
                        'order_id': order_id,
                        'country_id': country_id
                    },
                    xhr: function() {
                        const xhr = new XMLHttpRequest();
                        xhr.responseType= 'blob'
                        return xhr;
                    },
                    cache: "false",
                    success: function (data) {

                        var blob = new Blob([data]);
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = `Order_${order_no}.pdf`;
                        link.click();

                        window.open(`https://wa.me/+${manager_info[0].mobile}?text=*New order from Tele Sales created.Please process further to deliver it properly.For more details watch the attached pdf or visit:* http://sprod.sihirfms.com/tele-order-pdf/${order_id}`);
                    }
                })
            }
        }

        function mobileToClipboard(){
            let mobile = $('#outlet-mobile').val()

            if(mobile != '') {
                copyToClipboard(mobile)
                $('#copy-message').show()
                setTimeout(() => {
                    $('#copy-message').hide()
                }, 7000);
            }
        }

        function deleteRow(that, item_id){
            $(`#free-item-${item_id}`).remove();
            $(that).attr('disabled', 'disabled');
            $(that).parent().parent().remove();

            getTotal()
            getDiscountTotal()
            getGrvTotal()
        }

        function deletePromotionRows(that, prom_id){

            Swal.fire({
                title: 'Do you want to delete all items from this promotion?',
                showDenyButton: true,
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`.promotion-row-${prom_id}`).remove();
                    getTotal()
                }
            })

        }

        function clearInfo(){
            $('#outlet-name').val('');
            $('#outlet-name').attr('title', '');
            $('#outlet-code').val('');
            $('#outlet-code').attr('title', '');
            $('#outlet-district').val('');
            $('#outlet-district').attr('title', '');
            $('#outlet-thana').val('');
            $('#outlet-thana').attr('title', '');
            $('#outlet-mobile').val('');
            $('#outlet-address').val('');
            $('#order-list').html('');
            $('#item-footer').hide()
            $('#amim-id').empty();
            $('#items-list').empty();
            $('#grv-order-list').html('');
            $('#grv-amim-id').empty();
            $('#grv-items-list').empty();
            $('#grv-total-price').val('')
            $('#grv-item-footer').hide()
            $('.order-details-table').hide()
            $('#msp-history').empty()
            $('#msp-block').hide()
            $('#item-special-discount').removeAttr("value")
            $('#invoice-special-discount-percent').removeAttr("value")
            $('#invoice-special-discount-amount').removeAttr("value")
        }

        function copyToClipboard(textToCopy) {
            // navigator clipboard api needs a secure context (https)
            if (navigator.clipboard && window.isSecureContext) {
                // navigator clipboard api method'
                return navigator.clipboard.writeText(textToCopy);
            } else {
                // text area method
                let textArea = document.createElement("textarea");
                textArea.value = textToCopy;
                // make the textarea out of viewport
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                textArea.style.top = "-999999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                return new Promise((res, rej) => {
                    // here the magic happens
                    document.execCommand('copy') ? res() : rej();
                    textArea.remove();
                });
            }
        }

    </script>
@endsection