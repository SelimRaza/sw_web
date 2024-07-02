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
                                  action="{{url ('load/filter/sr_summary/demo2')}}"
                                  method="post" enctype="multipart/form-data" autocomplete="off">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}

                                <div class="row" >

                                    <input type="hidden" id="con"/>
                                    <input type="hidden" id="country_id"/>
                                    <input type="hidden" id="employee-id"/>
                                    <input type="hidden" id="sales-group-id"/>

                                    <div class="col-md-2 col-sm-2 shadow-div" style="height: 500px!important;">
                                        <ul class="nav">
                                            <li class="order-sidebar">
                                                <select class="form-control cmn_select2"
                                                        name="country_id" required="required" onchange="getCompanies()"
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
                                                        id="sr-id" onchange="getRoutes()">

                                                    <option value="">Select SR</option>
                                                </select>
                                            </li>
                                            <li class="order-sidebar">
                                                <select class="form-control cmn_select2"
                                                        name="rout_id" required="required"
                                                        id="rout-id" onchange="getOutlets()">

                                                    <option value="">Select Rout</option>
                                                </select>
                                            </li>
                                            <li class="order-sidebar">
                                                <select class="form-control cmn_select2"
                                                        name="outlet_id" required="required"
                                                        id="outlet-id" onchange="getOutletInfo();getSubCategories()">

                                                    <option value="">Select Outlet</option>
                                                </select>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="col-md-4 col-sm-4">
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

                                                    </div>
                                                </div>
                                            </div>

                                            <div id="order-history" class="col-md-12 " style="display: none; height: 380px !important;
                                            overflow-y: scroll;">
                                                <table class="table" id="order-table"
                                                       style="width: 97%; padding-top: 10px; padding-left: 5px; margin: 0">
                                                    <thead>
                                                    <tr class="tbl_header_light">
                                                        <th>SL</th>
                                                        <th>Date</th>
                                                        <th>Order Number</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="order-list">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-6 shadow-div" style="text-align: center; height: 500px!important;">
                                            <label for="has-note" style="margin-right: 5px; margin-left: 5px">
                                                <input type='radio' id="has-note" name='note' value="1" onclick="showNote()" > Note
                                            </label>

                                            <label for="non_productive">
                                                <input type='radio'  onclick="isNonProductive()" id="non_productive"
                                                       name='non_p_or_order'> <span style="margin-left: .01rem;"> Non Productive </span>
                                            </label>

                                            <label for="order" style=" margin-left: 5px">
                                                <input type='radio' name='non_p_or_order' onclick="isOrder()" id="order"><span style="margin-left: .01rem;">
                                                Order</span>
                                            </label><br>

                                            <div class="col-md-12 col-sm-12 col-xs-12 note-field" style="display: none">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                                       for="note" style="text-align: left">Note<span
                                                            class="required">*</span></label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <textarea class="form-control in_tg" name="note" id="note" autocomplete="off" required></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-sm-12 col-xs-12 non-productive-field" style="display: none; margin-bottom: .75rem">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                                       for="ttqs_type" style="text-align: left">Type<span
                                                            class="required">*</span></label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">

                                                    <select class="form-control cmn_select2" name="ttqs_type" id="ttqs_type" required>
                                                        <option value="">Select an Option</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-sm-12 col-xs-12 order-file"
                                                 style="display: none; margin-top: .75rem; text-align: center">

                                                <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 1rem">
                                                    <div class="col-md-6 col-sm-6 col-xs-6" style=" padding: 0">
                                                        <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                                               for="cat-id" style="text-align: left; padding: 0">Category<span
                                                                    class="required">*</span></label>
                                                        <div class="col-md-12 col-sm-12 col-xs-12" style=" padding: 0">

                                                            <select class="form-control cmn_select2" onchange="getItems()" name="cat_id" id="cat-id" required>
                                                                <option value="">Select a Category</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6 col-xs-6" style="padding: 0; padding-left: 5px">
                                                        <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                                               for="amim-id" style="text-align: left; padding: 0">Item<span
                                                                    class="required">*</span></label>
                                                        <div class="col-md-12 col-sm-12 col-xs-12" onchange="getOrders()" style=" padding: 0">

                                                            <select class="form-control cmn_select2" name="amim_id" id="amim-id" required>
                                                                <option value="">Select an Item</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top: .75rem; padding: 0;text-align: left">

                                                            <h4 class="text-center">Items</h4>

                                                            <div id="items" style="overflow-y: scroll; height: 270px">
                                                                <table class="table table-responsive table-borderless" style="border: none;overflow-y: scroll; max-height: 200px; padding-top: 10px; padding-left: 5px; margin: 0">
                                                                    <thead style="position: sticky; top: 0px;">
                                                                    <tr class="tbl_header_light">
                                                                        <th style="width: 50% !important; text-align: left">Item</th>
                                                                        <th style="width: 15% !important;">Pcs</th>
                                                                        <th style="width: 15% !important;">Ctn</th>
                                                                        <th style="width: 20% !important;">Subtotal</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody id="items-list">
                                                                    </tbody>
                                                                    <tfoot style=" position: sticky;
                                                                                   bottom: 0px;
                                                                                   z-index: 9;
                                                                                   height: initial;
                                                                                   background: white;">
                                                                    <tr id="item-footer" style="display: none">
                                                                        <td ></td>
                                                                        <td ></td>
                                                                        <td style="vertical-align: middle;">Total</td>
                                                                        <td >
                                                                            <input type="number" style="width: 100%; float: left; padding: 0" disabled id="total-price" name="total">
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
    </div>
    <style>
        .shadow-div{
            min-height: 50px;
            padding: 10px 0px 15px 10px;
            -webkit-box-shadow: 1px 1px 5px 2px rgb(0 0 0 / 21%);
            box-shadow: 1px 1px 5px 2px rgb(0 0 0 / 21%);
            margin-bottom: 15px;
        }

        #items::-webkit-scrollbar{
            display: none;
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
            width: 93%;
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
    </style>
    <script type="text/javascript">
        let token = $('#_token').val();

        $(document).ready(function () {

            $("select").select2();

            $('.order-file').hide()


        });

        function isNonProductive() {
            if($('#non_productive').is(":checked")){
                $('#order').prop('checked', false);
                $('.non-productive-field').show();
                $('.order-file').hide()


            }else if($('#order').is(":checked")){
                $('#non_productive').prop('checked', false);
            }else{
                $('.non-productive-field').hide();
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
                $("#has-note").attr("checked", true)
            }else{
                $(".note-field").hide()
                $("#has-note").attr("checked", false);
            }
        }

        function getCompanies(){
            let country = $('#country-id').select2('data')[0]

            let country_id = country.id
            clearInfo()
            $('#amim-id').empty();
            $('#cat-id').empty();
            $("#employee-id").val('')
            $('#sales-group-id').val('')
            $('#slgp-id').html(`<option value="" selected="" disabled="">Select Group</option>`)
            $('#zone-id').html(`<option value="" selected="" disabled="">Select Zone</option>`)
            $('#sr-id').html(`<option value="" selected="" disabled="">Select SR</option>`)
            $('#rout-id').html(`<option value="" selected="" disabled="">Select Rout</option>`)
            $('#outlet-id').html(`<option value="" selected="" disabled="">Select Outlet</option>`)

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

                        $('#ajax_load').css('display','none');
                        $('#acmp-id').empty();
                        var html='<option value="" selected disabled>Select BU</option>';
                        if(data){
                            for(var i=0;i<info.length;i++){
                                html+='<option value="'+info[i].id+'">'+info[i].acmp_code+'-'+info[i].acmp_name+'</option>';
                            }
                        }
                        $('#acmp-id').append(html);
                    },error:function(error){
                        $('#ajax_load').css('display','none');
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'No BU Found',
                        });
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

        function getGroups(){
            let company = $('#acmp-id').select2('data')[0]
            let company_id = company.id
            let con = $('#con').val()
            clearInfo()
            $('#amim-id').empty();
            $("#employee-id").val('')
            $('#sales-group-id').val('')
            $('#zone-id').html(`<option value="" selected="" disabled="">Select Zone</option>`)
            $('#sr-id').html(`<option value="" selected="" disabled="">Select SR</option>`)
            $('#rout-id').html(`<option value="" selected="" disabled="">Select Rout</option>`)
            $('#outlet-id').html(`<option value="" selected="" disabled="">Select Outlet</option>`)
            $('#cat-id').empty();

            getZones()

            if(company_id !=''){
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


                        $('#ajax_load').css('display','none');
                        $('#slgp-id').empty();
                        var html='<option value="" selected disabled>Select Group</option>';
                        if(info){
                            for(var i=0;i<info.length;i++){
                                html+='<option value="'+info[i].id+'">'+info[i].slgp_code+'-'+info[i].slgp_name+'</option>';
                            }
                        }
                        $('#slgp-id').append(html);
                    },error:function(error){
                        $('#ajax_load').css('display','none');
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'No Group Found',
                        });
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
            $('#cat-id').empty();
            $('#sr-id').html(`<option value="" selected="" disabled="">Select SR</option>`)
            $('#rout-id').html(`<option value="" selected="" disabled="">Select Rout</option>`)
            $('#outlet-id').html(`<option value="" selected="" disabled="">Select Outlet</option>`)
            $('#sales-group-id').val(slgp_id)
        }

        function getZones(){
            let con = $('#con').val()
            $("#employee-id").val('')
            $('#cat-id').empty();

            let country_id = $('#country_id').val()
            clearInfo()
            $('#amim-id').empty();
            $('#sr-id').html(`<option value="" selected="" disabled="">Select SR</option>`)
            $('#rout-id').html(`<option value="" selected="" disabled="">Select Rout</option>`)
            $('#outlet-id').html(`<option value="" selected="" disabled="">Select Outlet</option>`)

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


                        $('#ajax_load').css('display','none');
                        $('#zone-id').empty();
                        var html='<option value="" selected disabled>Select Zone</option>';
                        if(info){
                            for(var i=0;i<info.length;i++){
                                html+='<option value="'+info[i].id+'">'+info[i].zone_code+'-'+info[i].zone_name+'</option>';
                            }
                        }
                        $('#zone-id').append(html);
                    },error:function(error){
                        $('#ajax_load').css('display','none');
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'No Zone Found',
                        });
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

            $('#rout-id').html(`<option value="" selected="" disabled="">Select Rout</option>`)
            $('#outlet-id').html(`<option value="" selected="" disabled="">Select Outlet</option>`)

            let zone_id  = zone.id
            let slgp_id  = slgp.id
            clearInfo()
            $('#amim-id').empty();
            $('#rout-id').empty();
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


                        $('#ajax_load').css('display','none');
                        $('#sr-id').empty();
                        var html='<option value="" selected disabled>Select SR</option>';
                        if(info){
                            for(var i=0;i<info.length;i++){
                                html+='<option value="'+info[i].id+'">'+info[i].aemp_usnm+'-'+info[i].aemp_name+'</option>';
                            }
                        }
                        $('#sr-id').append(html);
                    },error:function(error){
                        $('#ajax_load').css('display','none');
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'No SR Found',
                        });
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
            $('#outlet-id').html(`<option value="" selected="" disabled="">Select Outlet</option>`)
            let aemp_id  = aemp.id

            clearInfo()


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


                        $('#ajax_load').css('display','none');
                        $('#rout-id').empty();
                        var html='<option value="" selected disabled>Select Rout</option>';
                        if(info){
                            for(var i=0;i<info.length;i++){
                                html+='<option value="'+info[i].id+'">'+info[i].rout_code+'-'+info[i].rout_name+'</option>';
                            }
                        }
                        $('#rout-id').append(html);
                    },error:function(error){
                        $('#ajax_load').css('display','none');
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'No Route Found',
                        });
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

            let route_id  = route.id

            clearInfo()

            if(con != '' && country_id  != '' && route_id  != ''){
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/getOutlets",
                    data:{
                        country_id: country_id,
                        con: con,
                        route_id: route_id,
                        _token: token,
                    },
                    cache:"false",
                    success:function(data){

                        $('#ajax_load').css('display','none');
                        $('#outlet-id').empty();
                        var html='<option value="" selected disabled>Select Outlet</option>';
                        if(data){
                            for(var i=0;i<data.length;i++){
                                html+='<option value="'+data[i].id+'">'+data[i].site_code+'-'+data[i].site_name+'</option>';
                            }
                        }
                        $('#outlet-id').append(html);
                    },error:function(error){
                        $('#ajax_load').css('display','none');
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'No Outlet Found',
                        });
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

        function getOutletInfo(){
            let con = $('#con').val()
            let country_id = $('#country_id').val()
            let employee_id = $('#employee-id').val()
            let outlet = $('#outlet-id').select2('data')[0]
            let outlet_id  = outlet.id

            clearInfo()

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
                            $('#outlet-code').val(info.site_code);
                            $('#outlet-district').val(info.district);
                            $('#outlet-thana').val(info.thana);
                            $('#outlet-mobile').val(info.mobile);
                        }
                    },error:function(error){
                        $('#ajax_load').css('display','none');
                        // Swal.fire({
                        //     icon: 'error',
                        //     title: 'Failed',
                        //     text: 'No Outlet Found',
                        // });
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
                                        <input type="hidden" value="${items.id}" class="spsb-id" name="spsb_ids[]">
                                    </td>
                                    <td>
                                        <span class="order-list-text">${data[i].ordm_ornm}</span>
                                        <input type="hidden" value="${items.id}" class="spsb-id" name="spsb_ids[]">
                                    </td>
                                    <td>
                                        <span class="order-list-text">${data[i].ordm_amnt}</span>
                                        <input type="hidden" value="${items.id}" class="spsb-id" name="spsb_ids[]">
                                    </td>
                                </tr>`
                            }

                            $('#order-list').html(order_items)

                        }else{
                            // Swal.fire({
                            //     icon: 'error',
                            //     title: 'Failed',
                            //     text: 'No Order',
                            // });
                        }
                    },error:function(error){
                        $('#ajax_load').css('display','none');
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'Invalid Information',
                        });
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

        function getSubCategories(){
            let country_id = $('#country_id').val()
            let slgp_id = $('#sales-group-id').val()


            clearInfo()
            $('#cat-id').empty();
            $('#amim-id').empty();
            $('#items-list').empty();


            if(country_id !='' && slgp_id){
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/getSubCategories",
                    data:{
                        country_id: country_id,
                        slgp_id: slgp_id,
                        _token: token,
                    },
                    cache:"false",
                    success:function(data){

                        let info = data.categories
                        let country_id = data.country_id

                        $('#country_id').val(country_id)

                        $('#ajax_load').css('display','none');
                        $('#cat-id').empty();
                        var html='<option value="" selected disabled>Select Category</option>';
                        if(data){
                            for(var i=0;i<info.length;i++){
                                html+='<option value="'+info[i].id+'">'+info[i].cat_code+'-'+info[i].cat_name+'</option>';
                            }
                        }
                        $('#cat-id').append(html);
                    },error:function(error){
                        $('#ajax_load').css('display','none');
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'No BU Found',
                        });
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

        function getItems(){
            let category = $('#cat-id').select2('data')[0]
            let category_id = category.id
            let country_id = $('#country_id').val()
            let sr_id = $('#employee-id').val()

            $('#amim-id').empty();


            if(country_id !='' && category_id !='' && sr_id !=''){
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/getItems",
                    data:{
                        country_id: country_id,
                        category_id: category_id,
                        sr_id: sr_id,
                        _token: token,
                    },
                    cache:"false",
                    success:function(data){

                        let info = data.items
                        let country_id = data.country_id

                        $('#country_id').val(country_id)

                        $('#ajax_load').css('display','none');
                        if(info && info.length>0){
                            $('#amim-id').empty();
                            var html='<option value="" selected disabled>Select an Item</option>';

                            for(var i=0;i<info.length;i++){
                                html += '<option value="' + info[i].amim_id + ':' + info[i].amim_name + ':' + info[i].amim_code + ':' + info[i].pldt_tppr +':'+ info[i].amim_duft+'">'+info[i].amim_id + '-' + info[i].amim_name+'</option>';
                            }

                            $('#amim-id').append(html);

                        }
                    },error:function(error){
                        $('#ajax_load').css('display','none');
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


            if(infos.length > 0){
                let amim_id = infos[0]

                $('.added-items-id').each(function() {
                    if($(this).val() === amim_id)
                    {
                        item_id_exist = false
                    }
                })

                if(item_id_exist) {

                    let amim_name = infos[1]
                    let amim_code = infos[2]
                    let amim_price = infos[3]
                    let amim_ctn_size = infos[4]

                    let item_lists = $('#items-list')

                    let items = `<tr>
                                <td>
                                    ${amim_code}-${amim_name}
                                    <input type="hidden" value="${amim_id}" class="added-items-id"  id="item-id-${amim_id}" name="item_ids[]">
                                    <input type="hidden" value="${amim_price}" id="item-price-${amim_id}">
                                    <input type="hidden" value="${amim_ctn_size}" id="ctn-size-${amim_id}">
                                </td>
                                <td>
                                    <input type="number"  id="item-qty-${amim_id}" style="width: 90%" onkeyup="getSubTotal('${amim_id}')" name="item_qtys[]">
                                </td>
                                <td>
                                    <input type="number" class="item-id" id="item-ctn-${amim_id}" style="width: 90%" onkeyup="getSubTotal('${amim_id}')"  name="item_ctns[]">
                                </td>
                                <td>
                                    <input type="number" disabled class="item-subtotal" style="width: 100%" id="item-subtotal-${amim_id}" name="item_prices[]">
                                </td>
                             </tr>`

                    item_lists.append(items)
                }
            }

        }

        function getSubTotal(amim_id){
            let item_price = Number($(`#item-price-${amim_id}`).val())
            let qty = Number($(`#item-qty-${amim_id}`).val())|0
            let ctn_size = Number($(`#ctn-size-${amim_id}`).val())|0
            let ctn_qty = Number($(`#item-ctn-${amim_id}`).val())|0
            let sub_total = (item_price*qty + ctn_size*ctn_qty*item_price).toFixed(2)

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

        function clearInfo(){
            $('#outlet-name').val('');
            $('#outlet-code').val('');
            $('#outlet-district').val('');
            $('#outlet-thana').val('');
            $('#outlet-mobile').val('');
            $('#order-list').html('');
            $('#item-footer').hide()
            $('#amim-id').empty();
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