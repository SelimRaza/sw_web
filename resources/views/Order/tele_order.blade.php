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
                            <strong>Order</strong>
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
                            <center><strong> ::: Outlet Details :::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <div class="x_panel">

                        <div class="x_content">
                            <form class="form-horizontal form-label-left"
                                  action="{{url ('tele/order')}}" id="tele-order"
                                  method="post" enctype="multipart/form-data" autocomplete="off">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}

                                <div class="row" >

                                    <input type="hidden" id="con"/>
                                    <input type="hidden" id="country_id"/>
                                    <input type="hidden" id="employee-id"/>
                                    <input type="hidden" id="sales-group-id"/>
                                    <input type="hidden" name="outlet_id" id="outlet-id"/>

                                    <div class="col-md-12 col-sm-12 shadow-div" style="">
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
                                            {{--                                            <li class="order-sidebar">--}}
                                            {{--                                                <select class="form-control cmn_select2"--}}
                                            {{--                                                        name="outlet_id" required="required"--}}
                                            {{--                                                        id="outlet-id" onchange="">--}}

                                            {{--                                                    <option value="">Select Outlet</option>--}}
                                            {{--                                                </select>--}}
                                            {{--                                            </li>--}}
                                        </ul>
                                    </div>

                                    <div class="col-md-4 col-sm-4" style="padding-left: 0">
                                        <div class="col-md-12 col-sm-12 shadow-div"  style="height: 500px!important;">

                                            <div id="sales_heirarchy" class="col-md-12 " style="height: 110px!important;">
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
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <i class="fa fa-clipboard fa-lg" onclick="mobileToClipboard()" style="color: green" aria-hidden="true"></i>
                                                        <span id="copy-message" style="display: none; margin-left: 0.45rem;" class="ml-2 text-success">Copied</span>
                                                        <a target="_blank" id="whats-app"
                                                           style="margin-left: 0.65rem;"
                                                           class="request_report_check ml-3">
                                                            <img style="width: 2.65rem;" src="{{asset('/theme/image/whatsapp.png')}}">
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="order-history" class="col-md-12 " style="display: none; ">
                                                <table class="table" id="order-table"
                                                       style="width: 97%; padding-top: 10px; padding-left: 5px; margin: 0">
                                                    <thead>
                                                    <tr class="tbl_header_light">
                                                        <th>SL</th>
                                                        <th>DATE</th>
                                                        <th>ORDER NUM</th>
                                                        <th>STATUS</th>
                                                        <th>AMNT</th>
                                                        <th>VIEW</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="order-list">
                                                    </tbody>
                                                </table>
                                                <div class="col-md-12 order-details-table" style="height: 200px !important;
                                                overflow-y: scroll; padding: 0">
                                                    <table class="table" id="order-details-table"
                                                           style="width: 97%; padding-top: 10px; padding-left: 5px; margin: 0">
                                                        <thead style="position: sticky; top: 0;">
                                                        <tr class="tbl_header_light">
                                                            <th style="width: 20%;">ITM CODE</th>
                                                            <th>ITM NAME</th>
                                                            <th>QTY</th>
                                                            <th style="width: 12%;">AMNT</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="order-details-list">
                                                        </tbody>
                                                </table>
                                                </div>
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

                                    <div class="col-md-8 col-sm-8 shadow-div" style="text-align: center; height: 500px!important;">
                                        <label for="has-note" style="margin-right: 5px; margin-left: 5px">
                                            <input type='radio' id="has-note" name='note' value="1" onclick="showNote()" > Note
                                        </label>

                                        <label for="non_productive">
                                            <input type='radio' onclick="isNonProductive()" id="non_productive" value="0"
                                                   name='non_p_or_order'> <span style="margin-left: .01rem;"> Non Productive </span>
                                        </label>

                                        <label for="order" style=" margin-left: 5px">
                                            <input type='radio' value="1" name='non_p_or_order' onclick="isOrder()" id="order"><span style="margin-left: .01rem;">
                                                Order</span>
                                        </label><br>

                                        <div class="col-md-12 col-sm-12 col-xs-12 note-field" style="display: none">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="note-type" style="text-align: left">
                                                Note Type<span class="required">*</span></label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <select class="form-control cmn_select2" name="ntpe_id" id="note-type">
                                                    <option value="">Select a Country First</option>
                                                </select>
                                            </div>
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                                   for="note" style="text-align: left">Note<span
                                                        class="required">*</span></label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <textarea class="form-control in_tg" name="order_note" id="note" autocomplete="off"></textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-sm-12 col-xs-12 non-productive-field" style="display: none; margin-bottom: .75rem">
                                            <input type="hidden" name="npro_note" id="npro-note"/>

                                            <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                                   for="non-productive-type" style="text-align: left">Type<span
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

                                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 1rem">
                                                <div class="col-md-6 col-sm-6 col-xs-6" style=" padding: 0">
                                                    <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                                           for="cat-id" style="text-align: left; padding: 0">Category<span
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
                                                            <option value="">Select an Item</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top: .75rem; padding: 0;text-align: left">

                                                    <h4 class="text-center">Items</h4>

                                                    <div id="items" style="overflow-y: scroll; height: 350px">
                                                        <table class="table table-responsive table-borderless" style="border: none;overflow-y: scroll; max-height: 200px; padding-top: 10px; padding-left: 5px; margin: 0">
                                                            <thead style="position: sticky; top: 0px;">
                                                            <tr class="tbl_header_light">
                                                                <th style="width: 45% !important; text-align: left">Item</th>
                                                                <th style="width: 15% !important;">Pcs</th>
                                                                <th style="width: 15% !important;">Ctn</th>
                                                                <th style="width: 20% !important;">Subtotal</th>
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
                                                            <tr id="item-footer" style="display: none">
                                                                <td ></td>
                                                                <td ></td>
                                                                <td style="vertical-align: middle;">Total</td>
                                                                <td style="padding: 1.5px;">
                                                                    <input type="number" style="width: 100%; text-align: end;; padding: 0" readonly class="in_tg" id="total-price" name="total">
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
                            </form>
                        </div>

                    </div>

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

        .fa-times-circle-o:hover, .fa-eye:hover, #whats-app:hover{
            cursor: pointer;
        }
    </style>
    <script type="text/javascript">
        let token = $('#_token').val();

        let outlets = [];
        let total_outlet;

        $(document).ready(function () {

            $("select").select2();

            $('.order-file').hide()


        });

        function isNonProductive() {
            if($('#non_productive').is(":checked")){
                $('#order').prop('checked', false);
                $('.non-productive-field').show();
                $('.order-file').hide()
                $('#items-list').empty()
                $('#total-price').val('')


            }else if($('#order').is(":checked")){
                $('#non_productive').prop('checked', false);
            }else{
                $('.non-productive-field').hide();
                $('#non-productive-type').val('').trigger('change');

            }
        }

        function isOrder() {
            if($('#order').is(":checked")){
                $('#non_productive').prop('checked', false);
                $('.order-file').show()
                $('.non-productive-field').hide();
            }else{
                $('.order-file').hide()
            }
        }

        let note_shown = false

        function showNote() {
            note_shown = !note_shown

            if(note_shown){
                $(".note-field").show()
                $("#items").css('height','210px')
                $("#has-note").attr("checked", true)
            }else{
                $(".note-field").hide()
                $("#items").css('height','350px')
                $("#has-note").attr("checked", false);
            }
        }

        function getCompanies(){
            let country = $('#country-id').select2('data')[0]
            let country_id = country.id
            outlets = []
            $('#outlet-serial').empty()
            $('#country_id').val('')


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
            $("#employee-id").val('')
            $('#cat-id').empty();
            $('#plmt-id').val('')
            $('#outlet-serial').empty()

            outlets = []

            let country_id = $('#country_id').val()
            clearInfo()
            $('#amim-id').empty();
            $('#zone-id').html(`<option value="" selected>Select Zone</option>`)
            $('#sr-id').html(`<option value="" selected>Select SR</option>`)
            $('#dlrm-id').html(`<option value="" selected>Select Distributor</option>`)
            $('#rout-id').html(`<option value="" selected>Select Rout</option>`)
            $('#outlet-id').val('')

            if(country_id !=''){
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/getZones",
                    data:{
                        country_id: country_id,
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

        function getOutlets(){
            let con = $('#con').val()
            let country_id = $('#country_id').val()
            let route = $('#rout-id').select2('data')[0]
            let employee_id = $('#employee-id').val()
            $('#outlet-serial').empty()
            $('.order-details-table').hide()
            let route_id  = route.id

            clearInfo()

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
                            getOutletInfo(outlets[0].id);
                            $('#order-details-list').empty()
                            getSubCategories(outlets[0].id)
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

        function getOutletInfo(outlet_id = null){
            let con = $('#con').val()
            let country_id = $('#country_id').val()
            let employee_id = $('#employee-id').val()
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
                            $('#outlet-thana').val(info.thana);
                            $('#outlet-mobile').val(info.mobile);
                            $('#whats-app').attr('href', `https://wa.me/${info.mobile}`);
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

                        $('#order-history').show()
                        $('#order-list').empty()


                        if(data && data.length>0){
                            let order_items;

                            for(let i=0; i<data.length; i++) {
                                let count = i;
                                order_items += `
                                <tr>
                                    <td>
                                        <span class="order-list-text">${++count}</span>
                                    </td>
                                    <td>
                                        <span class="order-list-text">${data[i].ordm_date}</span>
                                        <input type="hidden" value="${items.id}">
                                    </td>
                                    <td>
                                        <span class="order-list-text">${data[i].ordm_ornm}</span>
                                        <input type="hidden" value="${items.id}">
                                    </td>
                                    <td>
                                        <span class="order-list-text">${data[i].order_status}</span>
                                    </td>
                                    <td style="text-align: end;">
                                        <span class="order-list-text">${data[i].ordm_amnt}</span>
                                        <input type="hidden" value="${items.id}">
                                    </td>
                                    <td>
                                        <i class="fa fa-eye fa-sm " onclick="getOrderDetails('${data[i].id}')" style="float:right;"></i>
                                    </td>
                                </tr>`
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

        function getSubCategories(site_id){
            let country_id = $('#country_id').val()
            let slgp_id = $('#sales-group-id').val()
            let sr_id = $('#employee-id').val()

            let info = {
                country_id: country_id,
                slgp_id: slgp_id,
                sr_id: sr_id,
                _token: token
            }

            if(site_id != ''){
                info = {
                    country_id: country_id,
                    slgp_id: slgp_id,
                    site_id: site_id,
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


                        $('#cat-id').empty();
                        let html='<option value="" selected>Select Category</option>';
                        if(info.length>0){
                            if(country_id === 2 || country_id === 5)
                            {
                                for (var i = 0; i < info.length; i++) {
                                    html += `<option value="${info[i].id}:${info[i].aemp_id}:${info[i].rout_id}:${info[i].slgp_id}">${info[i].cat_code}-${info[i].cat_name}</option>`;
                                }
                            }else{
                                let plmt_id = info[0].plmt_id
                                $('#plmt-id').val(plmt_id)
                                for (var i = 0; i < info.length; i++) {
                                    html += `<option value="${info[i].id}">${info[i].cat_code}-${info[i].cat_name}</option>`;
                                }
                            }
                        }

                        $('#cat-id').append(html);
                    }
                });
            }
            else{
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please Select a Country First !!!',
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
                                    html += `<option value="${info[i].amim_id}:${info[i].amim_name}:${info[i].amim_code}:${info[i].pldt_tppr}:${info[i].amim_duft}:${sr_id}:${rout_id}:${info[i].plmt_id}:${slgp_id}">${info[i].amim_code}-${info[i].amim_name}</option>`;
                                }
                            }else{
                                for (var i = 0; i < info.length; i++) {
                                    html += `<option value="${info[i].amim_id}:${info[i].amim_name}:${info[i].amim_code}:${info[i].pldt_tppr}:${info[i].amim_duft}">${info[i].amim_code}-${info[i].amim_name}</option>`;
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

        function getOrders(){
            let item_id_exist = true
            $('#item-footer').show()
            let amim = $('#amim-id').select2('data')[0]
            let amim_info = amim.id
            let infos = amim_info.split(':')
            let country_id = $('#country_id').val()



            if(infos.length > 0){
                let amim_id = infos[0]

                $('.added-items-id').each(function() {
                    if($(this).val() === amim_id)
                    {
                        item_id_exist = false
                    }
                })

                if(item_id_exist) {
                    let items           = '';
                    let amim_name       = infos[1]
                    let amim_code       = infos[2]
                    let amim_price      = infos[3]
                    let amim_ctn_size   = infos[4]

                    let item_lists = $('#items-list')


                    if(country_id === '2' || country_id === '5') {
                        let sr_id           = infos[5]
                        let rout_id         = infos[6]
                        let plmt_id         = infos[7]
                        let slgp_id         = infos[8]
                        items = `<tr>
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
                                <input type="number" class="in_tg" id="item-qty-${amim_id}" style="width: 90%; text-align: end;" onkeyup="getSubTotal('${amim_id}')" name="item_qtys[]">
                            </td>
                            <td>
                                <input type="number" class="item-id in_tg" id="item-ctn-${amim_id}" style="width: 90%; text-align: end;" onkeyup="getSubTotal('${amim_id}')"  name="item_ctns[]">
                            </td>
                            <td>
                                <input type="number" readonly class="item-subtotal in_tg" style="width: 100%; text-align: end;" id="item-subtotal-${amim_id}" name="item_prices[]">
                            </td>
                            <td style="text-align: center">
                                <i class="fa fa-times-circle-o text-danger" aria-hidden="true" onclick="deleteRow(this)"></i>
                            </td>
                     </tr>`
                    }
                    else{
                        items = `<tr>
                                <td>
                                    ${amim_code}-${amim_name}
                                    <input type="hidden" value="${amim_id}" class="added-items-id"  id="item-id-${amim_id}" name="item_ids[]">
                                    <input type="hidden" value="${amim_price}" name="item_unit_prices[]"  id="item-price-${amim_id}">
                                    <input type="hidden"  name="item_dufts[]" value="${amim_ctn_size}" id="ctn-size-${amim_id}">
                                </td>
                                <td>
                                    <input type="hidden" id="total-item-${amim_id}" name="total_qtys[]">
                                    <input type="number" class="in_tg" id="item-qty-${amim_id}" style="width: 90%; text-align: end;" onkeyup="getSubTotal('${amim_id}')" name="item_qtys[]">
                                </td>
                                <td>
                                    <input type="number" class="item-id in_tg" id="item-ctn-${amim_id}" style="width: 90%; text-align: end;" onkeyup="getSubTotal('${amim_id}')"  name="item_ctns[]">
                                </td>
                                <td>
                                    <input type="number" readonly class="item-subtotal in_tg" style="width: 100%; text-align: end;" id="item-subtotal-${amim_id}" name="item_prices[]">
                                </td>
                                <td style="text-align: center">
                                    <i class="fa fa-times-circle-o text-danger" aria-hidden="true" onclick="deleteRow(this)"></i>
                                </td>
                             </tr>`
                    }

                    item_lists.append(items)
                }
            }

        }

        function getSubTotal(amim_id){
            let item_price = Number($(`#item-price-${amim_id}`).val())
            let qty = Number($(`#item-qty-${amim_id}`).val())|0
            let ctn_size = Number($(`#ctn-size-${amim_id}`).val())|0
            let ctn_qty = Number($(`#item-ctn-${amim_id}`).val())|0
            let total_qty =  qty+(ctn_size)*ctn_qty
            let sub_total = (item_price*qty + ctn_size*ctn_qty*item_price).toFixed(2)

            $(`#total-item-${amim_id}`).val(total_qty)
            $(`#item-subtotal-${amim_id}`).val(sub_total)

            getTotal()
        }

        function getTotal(){
            let total = 0
            $('.item-subtotal').each(function (){
                total+=Number($(this).val())
            })

            $('#total-price').val(total.toFixed(2))
        }

        function setNonProductiveNote(){
            let npro_type = $('#non-productive-type').select2('data')[0]

            $('#npro-note').val(npro_type.text)
        }

        function storeTeleOrder(){
            let order_info = $('#tele-order').serialize()
            $.ajax({
                type:"POST",
                url:"{{URL::to('/')}}/tele/order",
                data: order_info,
                cache:"false",
                success:function(data){

                    outlets.shift()

                    if(data.hasOwnProperty('visited_outlet_in_rout') && data.visited_outlet_in_rout !== null) {
                        $('#outlet-serial').html(`${data.visited_outlet_in_rout} out of ${total_outlet}`)
                    }

                    if(outlets.length>0){
                        getOutletInfo(outlets[0].id)
                    }

                    $('#items-list').empty()
                    $('#order-details-list').empty()

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `${data.success}`,
                    });

                },error:function(error) {
                    if(error.responseJSON.hasOwnProperty('exist') && error.responseJSON.exist === 1){
                        $('#items-list').empty()

                        $('#outlet-serial').html(`${error.responseJSON.visited_outlet_in_rout} out of ${total_outlet}`)

                        outlets.shift()

                        if(outlets.length>0){
                            getOutletInfo(outlets[0].id)
                            $('#order-details-list').empty()
                        }
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: error.responseJSON.error,
                    });
                }
            });
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

        function deleteRow(that){
            $(that).attr('disabled', 'disabled');
            $(that).parent().parent().remove();

            getTotal()
        }

        function clearInfo(){
            $('#outlet-name').val('');
            $('#outlet-name').attr('title', '');
            $('#outlet-code').val('');
            $('#outlet-code').attr('title', '');
            $('#outlet-district').val('');
            $('#outlet-thana').val('');
            $('#outlet-mobile').val('');
            $('#order-list').html('');
            $('#item-footer').hide()
            $('#amim-id').empty();
            $('#items-list').empty();
            $('.order-details-table').hide()
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