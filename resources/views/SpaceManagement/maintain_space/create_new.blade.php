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
                            <a href="{{ URL::to('/maintain/space')}}">Space List</a>
                        </li>
                        <li class="active">
                            <strong>New Space Program</strong>
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
                    
                {{-- Space Program --}}
                <div class="row">

                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">

                            <div class="x_content">

                                <form class="form-horizontal form-label-left" action="{{URL::to('maintain/space')}}"
                                      method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    <div id="wizard" class="form_wizard wizard_horizontal">
                                        <ul class="wizard_steps">
                                            <li>
                                                <a href="#step-1">
                                                    <span class="step_no">1</span>
                                                    <span class="step_descr">
                                              <strong>Create Program</strong>
                                          </span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#step-2">
                                                    <span class="step_no">2</span>
                                                    <span class="step_descr">
                                              <strong>Zone Mapping</strong>
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

                                        </ul>
                                        <div class="x_title">
                                            <div class="clearfix"></div>
                                        </div>
                                        <div id="step-1" style="height: 300px">
                                            <div class="item form-group">
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="spcm_name">Program
                                                    Name <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <input id="name" name="spcm_name" class="form-control col-md-7 col-xs-12 in_tg"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           placeholder="Enter Promotion Name" required="required"
                                                           type="text">
                                                </div>


                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Sales
                                                    Group
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control cmn_select2" name="slgp_id" id="slgp_idds" onchange="getCategory(this.value)"
                                                            required >

                                                        <option value="">Select Sales Group</option>
                                                        @foreach ($acmp as $salesGroups)
                                                            <option value="{{ $salesGroups->slgp_id }}">{{ $salesGroups->slgp_code.' - '.$salesGroups->slgp_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

{{--                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Program--}}
{{--                                                    Code <span--}}
{{--                                                            class="required">*</span>--}}
{{--                                                </label>--}}
{{--                                                <div class="col-md-4 col-sm-4 col-xs-12">--}}
{{--                                                    <input id="Code" name="promotion_code" class="form-control col-md-7 col-xs-12"--}}
{{--                                                           data-validate-length-range="6" data-validate-words="2"--}}
{{--                                                           placeholder="Enter Promotion Code" required--}}
{{--                                                           type="text">--}}
{{--                                                </div>--}}

                                            </div>

                                            <div class="item form-group">
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" id="startDate" for="name">Start
                                                    Date <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <input name="spcm_sdat"
                                                           class="form-control col-md-7 col-xs-12 in_tg date"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           value="{{ date('Y-m-d')}}" required="required" type="text">
                                                </div>

                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" id="endDate" for="name">End
                                                    Date <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <input name="spcm_edat"
                                                           class="form-control col-md-7 col-xs-12 in_tg date"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           value="{{ date('Y-m-d')}}"  required="required" type="text">
                                                </div>

                                            </div>

                                            <div class="form-group">
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="spcm_imge">Regulations </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <input name="spcm_imge" id="space-image" onchange="regulationImage()"
                                                           class="form-control col-md-7 col-xs-12 in_tg"
                                                           required="required" type="file">
                                                    <span class="text-danger"> Image Size must be 512x512 </span>
                                                </div>

{{--                                                <div>--}}
{{--                                                    <img id="space-preview-image" style="display: none" src="image_path">--}}
{{--                                                </div>--}}
                                            </div>

                                        </div>

                                        <div id="step-2" style="height: 510px; margin-bottom: 25px !important;">
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
                                                <input type="hidden" id="gift_is_national" value="1" name="gift_is_national" />
                                                <input type="hidden" id="amnt_is_national" value="1" name="amnt_is_national" />

                                                <div class="col-md-2 col-sm-2 col-xs-12">
                                                    <input type="radio" checked="checked" value="1" id="id_national"
                                                           name="is_national"
                                                           onchange="showPromotionArea(this.value);"> Nationally <br/>
                                                    <input type="radio" value="0" id="id_zonal"
                                                           name="is_national"
                                                           onchange="showPromotionArea(this.value);"> Zonal
                                                </div>

                                                <div class="col-md-2 col-sm-2 col-xs-12 national">
                                                    <a class="btn btn-danger btn-sm" href="{{ URL::to('space-zone-format')}}"><span
                                                                class="fa fa-cloud-download"
                                                                style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Download
                                                            Format</b></a>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-12">
                                                    <input id="national-zone-upload" class="form-control national col-md-12 col-xs-12 in_tg"
                                                           name="file_upload" type="file">
                                                </div>

                                                <div class="col-md-2 col-sm-2 col-xs-12 national" onclick="loadZones(event)" style="float: right">
                                                    <a class="btn btn-info btn-sm"><b>Load</b></a>
                                                </div>
                                            </div>

                                            {{-- National Selection - All Zones--}}
                                            <div class="item" id="national" style="margin-bottom: .65rem">
                                                @foreach ($zones as $zone)
                                                    <div class="form-group">
                                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Zone
                                                            <span class="required">*</span>
                                                        </label>
                                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                                            <input type="hidden" id="zone-code-{{$zone->zone_code}}" value="{{ $zone->zone_id }}" name="national_zone_ids[]"  />

                                                            <input class="form-control zone-ids col-md-7 col-xs-12 in_tg"
                                                                   placeholder="Zone" required="required" value="{{ $zone->zone_code.' - '.$zone->zone_name }}"
                                                                   type="text" readonly>
                                                        </div>
                                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Quantity
                                                            <span class="required">*</span>
                                                        </label>
                                                        <div class="col-md-2 col-sm-2 col-xs-12">
                                                            <input class="form-control col-md-7 col-xs-12 in_tg"
                                                                   id="zone-qty-{{$zone->zone_code}}" required="required" name="national_zone_qtys[]"
                                                                   type="number">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- Zone Selection --}}
                                            <div id="myDiv">
                                                <div class="item form-group" >
                                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Select
                                                        Zone
                                                        <span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <select class="form-control cmn_select2 area_item" name="area_list[]" required>
                                                            <option value="">Select Zone</option>
                                                            @foreach ($zones as $zone)
                                                                <option value="{{ $zone->zone_id }}">{{ $zone->zone_code.' - '.$zone->zone_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Quantity
                                                        <span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-3 col-sm-3 col-xs-12">
                                                        <input class="form-control zone-qty col-md-7 col-xs-12 in_tg" onkeydown="newZonebyTab(event)"
                                                                required="required" name="individual_zone_qtys[]"
                                                               type="number">
                                                    </div>
                                                    <label class="control-label col-md-1 text-info col-sm-1 col-xs-12" for="name" style="display: flex;
                                                    justify-content: space-evenly;">
                                                        <i class="fa fa-plus-circle fa-lg" onclick="addNewZone()"></i>
                                                        <i class="fa fa-times-circle-o fa-lg text-danger zone-delete" aria-hidden="true" onclick="deleteRow(this)"></i>
                                                    </label>
                                                </div>
                                            </div>

                                        </div>

                                        <div id="step-3" style="height: 750px">
                                            <strong>
                                                <center><span style="color: #d80229;"> ::: Add item :::</span></center>
                                            </strong>
                                            <hr/>
                                            <div class="item form-group">
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Showcase/Offer:
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control cmn_select2" id="show-type"
                                                            required>
                                                        <option value="1">Showcase Items</option>
                                                        <option value="3">Offer Item</option>

                                                    </select>
                                                </div>

                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Item
                                                    Category
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control"
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
                                                    <select class="form-control" id="add_item_item"
                                                            onchange="getItemsInfos()"
                                                            required>

                                                        <option value="">Select Item</option>
                                                    </select>
                                                </div>
                                            </div>
{{--                                            <div class="item form-group">--}}
{{--                                                <center>--}}
{{--                                                    <button type="button" class="btn btn-warning"--}}
{{--                                                            onclick="addRowItemAssign()">Add--}}
{{--                                                        Item--}}
{{--                                                    </button>--}}
{{--                                                </center>--}}
{{--                                            </div>--}}
                                            <br/>
                                            <hr/>

                                            <strong>
                                                <center><span style="color: #d80229;"> ::: Showcase items :::</span>
                                                </center>
                                            </strong>
                                            <br/>
                                            <div class="item form-group">
                                                <table id="myTableOrder"
                                                       class="table table-bordered table-striped projects">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 15%;">Item Code</th>
                                                        <th>Item Name</th>
                                                        <th style="width: 20%;">Quantity</th>
                                                        <th style="width: 10%;">Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="showcase-items-list">

                                                    </tbody>
                                                </table>
                                            </div>

                                            <hr/>

                                            <strong>
                                                <center><span style="color: #d80229;"> ::: Offer item :::</span>
                                                </center>
                                            </strong>
                                            <br/>
                                            <div class="item form-group">
                                                <table id="myTableOrder"
                                                       class="table table-bordered table-striped projects">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 15%;">Item Code</th>
                                                        <th>Item Name</th>
                                                        <th style="width: 20%;">Quantity</th>
                                                        <th style="width: 10%;">Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="offer-item-list">

                                                    </tbody>
                                                </table>
                                            </div>

                                            <hr/>
                                            <strong>
                                                <center><span style="color: #d80229;"> ::: Free Amount ::: </span></center>
                                            </strong>
                                            <br/>

                                            <div class="item form-group col-md-12 col-sm-12" style="display: flex; justify-content: center">
                                                <input id="spft_amnt" class="form-control col-md-7 col-xs-12 in_tg"
                                                       style="width: 30%; margin-bottom: 15px"
                                                name="spft_amnt"
                                                placeholder="Amount" type="number">
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
    <style>
        .fa-times-circle-o:hover, .fa-plus-circle:hover{
            cursor: pointer;
        }

        #radio:hover{
            color: #73879C;
            cursor: pointer;
        }

        /*.stepContainer{*/
        /*    max-height: 700px !important;*/
        /*}*/
    </style>

    <script>
        $(document).ready(function (){

            $('.date').datepicker({
                dateFormat: 'yy-mm-dd',
                autoclose: 1,
                showOnFocus: true
            });

        })

        $('#myDiv').hide();
        $("#slgp_idds").select2();
        $("#add_item_category").select2();
        $("#add_item_item").select2();
        $("#slgp_id").select2();
        $(".cmn_select2").select2();
        let zones = @json($zones);

        function regulationImage() {
            let fileInput = document.getElementById("space-image");
            let file = fileInput.files[0];
            let image = $("#space-preview-image");
            let objectUrl = URL.createObjectURL(file);
            image.attr("src", objectUrl);
            image.on("load", function() {
                let width = image.width();
                let height = image.height();
                if(height !== 512 || width !== 512) {
                    $('#space-image').css('border', '1px solid red')
                    // $('#space-image').val('')
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: `Image Size Must be 512 x 512. Your Image height: ${height} and width: ${width}`,
                    });
                }else{
                    $('#space-image').css('border', '1px solid #ccc')
                }
            });
        }

        $('.buttonFinish').click(function (){
            console.log('button clicked', validImage)
        })


        function getItemsInfos(){

            let items = $('#add_item_item').select2('data')[0]

            let type = $('#show-type').select2('data')[0]

            let item_info = items.text.split('-')
            let item_name = item_info[1]
            let item_code = item_info[0]
            let item_id = items.id.split('-')[0]

            let spsb_id_exist = true;


            $('.spsb-id').each(function() {
                if($(this).val() === item_id)
                {
                    console.log($(this).val())
                    spsb_id_exist = false
                }
            })

            let spft_id_exist = true;

            $('.spft-id').each(function() {
                if($(this).val() === item_id)
                {
                    console.log($(this).val())
                    spft_id_exist = false
                }
            })

            if(!type.id)
            {
                Swal.fire({
                    title: 'Failed',
                    text: 'please select type first!',
                });
            }

            if(type.id && type.id === '3' && spft_id_exist){

                $('#offer-item-list').append(`
                <tr>
                    <td>
                        ${item_code}
                        <input type="hidden" value="${item_id}" class="spft-id" name="spft_ids[]">
                    </td>
                    <td>
                        ${item_name}
                    </td>
                    <td>
                        <input name="spft_min_qtys[]" class="form-control col-md-7 col-xs-12 in_tg" type="number"
                               value="1" style="width: 40%; height: 2.95rem;"
                    </td>
                    <td>
                        <i class="fa fa-times-circle-o text-danger" aria-hidden="true" onclick="deleteRow(this)"></i>
                    </td>
                </tr>`)
            }

            if(type.id && type.id === '1' && spsb_id_exist){
                $('#showcase-items-list').append(`
                <tr>
                    <td>
                        ${item_code}
                        <input type="hidden" value="${item_id}" class="spsb-id" name="spsb_ids[]">
                    </td>
                    <td>
                        ${item_name}
                    </td>
                    <td>
                        <input name="spsb_min_qtys[]" class="form-control col-md-7 col-xs-12 in_tg" type="number"
                               value="1" style="width: 40%; height: 2.95rem;"
                    </td>
                    <td>
                        <i class="fa fa-times-circle-o text-danger" aria-hidden="true" onclick="deleteRow(this)"></i>
                    </td>
                </tr>`)
            }
        }

        function deleteRow(that){
            let isZoneDelete = $(that).hasClass('zone-delete');

            if(isZoneDelete){
                if($('.zone-delete').length > 1){
                    $(that).attr('disabled', 'disabled');
                    $(that).parent().parent().remove();
                }
            }else {
                $(that).attr('disabled', 'disabled');
                $(that).parent().parent().remove();
            }
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
            if (type == '0') {
                $('#myDiv').show();
                $('#national').hide();
                $('#gift_is_national').val(0);
                $('#amnt_is_national').val(0);
                $('.national').hide();
            }
            else {
                $('#gift_is_national').val(1);
                $('#amnt_is_national').val(1);
                $('#myDiv').hide();
                $('#national').show();
                $('.national').show();
            }
        }

        function newZonebyTab(event){
            if(event.keyCode === 9){
                addNewZone()
            }
        }

        function addNewZone(){

            let exist_zones = [];

            if(zones.length > 0 ) {
                let zoneOptions = ''

                let selected_areas = $('.area_item').serializeArray()

                let selected_zone_ids = selected_areas.reduce((zones, zone_info)=> zones.concat(Number(zone_info.value)), [])

                zones.forEach((zone, index) => {
                    if($.inArray(zone.zone_id, selected_zone_ids) === -1) {
                        zoneOptions += `<option value="${zone.zone_id}">${zone.zone_code} - ${zone.zone_name} </option>`
                    }
                })

                $('#myDiv').append(`
                    <div class="item form-group" >
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Select
                            Zone
                            <span class="required">*</span>
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <select class="form-control cmn_select2 area_item" name="area_list[]" required>
                                <option value="">Select Zone</option>
                                ${zoneOptions}
                            </select>
                        </div>
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Quantity
                            <span class="required">*</span>
                        </label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input class="form-control zone-qty col-md-7 col-xs-12 in_tg" onkeydown="newZonebyTab(event)"
                                    required="required" name="individual_zone_qtys[]"
                                   type="number">
                        </div>
                        <label class="control-label col-md-1 text-info col-sm-1 col-xs-12" for="name" style="display: flex;
                                    justify-content: space-evenly;">
                            <i class="fa fa-plus-circle fa-lg" onclick="addNewZone()"></i>
                            <i class="fa fa-times-circle-o fa-lg text-danger zone-delete" aria-hidden="true" onclick="deleteRow(this)"></i>
                        </label>
                    </div>`);

                $(".cmn_select2").select2();


            {{--<option value="{{ $zone->zone_id }}">{{ $zone->zone_code.' - '.$zone->zone_name }}</option>--}}
{{--                @endforeach--}}
            }


        }

        function loadZones(event){
            var files = document.getElementById('national-zone-upload').files;
            if(files.length==0){
                console.log("Please choose any file...");
                return;
            }
            var filename = files[0].name;
            var extension = filename.substring(filename.lastIndexOf(".")).toUpperCase();
            if (extension == '.XLS' || extension == '.XLSX') {
                //Here calling another method to read excel file into json
                console.log('got and excel file')
                excelFileToJSON(files[0]);
            }else{
                alert("Please select a valid excel file.");
            }
        }

        function excelFileToJSON(file){
            try {
                var reader = new FileReader();
                reader.readAsBinaryString(file);
                reader.onload = function(e) {

                    var data = e.target.result;
                    var workbook = XLSX.read(data, {
                        type : 'binary'
                    });
                    var result = {};
                    var firstSheetName = workbook.SheetNames[0];
                    //reading only first sheet data
                    var jsonData = XLSX.utils.sheet_to_json(workbook.Sheets[firstSheetName]);
                    //displaying the json result into HTML table
                    console.log(jsonData)
                    displayJsonToHtmlTable(jsonData);
                }
            }catch(e){
                console.error(e);
            }
        }

        function displayJsonToHtmlTable(zone_info){
            console.log(zone_info)
            if(zone_info.length>0){
                for(var i=0;i<zone_info.length;i++){
                    let zone_qty = $(`#zone-qty-${zone_info[i]['zone_code']}`)
                    zone_qty.val(zone_info[i]['quantity'])
                }
            }else{
                console.log('No data in excel')
            }
        }

    </script>

@endsection