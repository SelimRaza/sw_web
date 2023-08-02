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
                            <a href="{{ URL::to('/maintain/space')}}">Space Maintain List</a>
                        </li>
                        <li class="active">
                            <strong>New Space Maintain</strong>
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
                    
                {{-- Space Maintain --}}
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
                                <div id="exTab1" class="container">
                                    <ul class="nav nav-pills">
                                        <li>
                                            <a href="#1a" data-toggle="tab"
                                                onclick="showSpaceMaintain()" selected>Add Space</a>
                                        </li>
                                        <li>
                                            <a href="#3a" data-toggle="tab" onclick="getSpaceArea()">
                                                Zone Mapping</a>
                                        </li>
                                        <li>
                                            <a href="#2a" data-toggle="tab" onclick="showShowcaseArea()">
                                                Item Mapping</a>
                                        </li>
{{--                                        <li>--}}
{{--                                            <a href="#4a" data-toggle="tab" onclick="getSiteMapping()">--}}
{{--                                                Site Mapping</a>--}}
{{--                                        </li>--}}
                                    </ul>

                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div id="1a" class="x_content">
                                <form class="form-horizontal form-label-left spcm" action="{{URL::to('maintain-space-store')}}"
                                      method="post" enctype="multipart/form-data" id="spcm">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                   
                                        <div class="animate__animated animate__zoomIn">
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="dfdm_name">Space
                                                    Name <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="spcm_name" name="spcm_name" class="form-control col-md-7 col-xs-12 in_tg"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           placeholder="Space  Name" required
                                                           type="text" value="{{ old('spcm_name') }}">
                                                </div>
                                            </div>
{{--                                            <div class="item form-group">--}}
{{--                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Space--}}
{{--                                                    Code <span--}}
{{--                                                            class="required">*</span>--}}
{{--                                                </label>--}}
{{--                                                <div class="col-md-6 col-sm-6 col-xs-12">--}}
{{--                                                    <input id="spcm_code" name="spcm_code" class="form-control col-md-7 col-xs-12 in_tg"--}}
{{--                                                           data-validate-length-range="6" data-validate-words="2"--}}
{{--                                                           placeholder=" Space Code" required="required"--}}
{{--                                                           type="text" value="{{ old('spcm_code') }}">--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                       for="slgp_id">Select Group<span
                                                            class="required">&nbsp;*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2"
                                                            name="slgp_id" required="required"
                                                            id="slgp_id">

                                                        <option value="">Select Group</option>
                                                        @foreach($acmp as $acmpList)
                                                            <option value="{{$acmpList->id}}">{{$acmpList->slgp_code}}
                                                                - {{$acmpList->slgp_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="start_date">Start
                                                    Date <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="start_date" name="spcm_sdat"
                                                           class="form-control col-md-7 col-xs-12 in_tg date"
                                                           value="{{ date('Y-m-d') }}" required="required" type="text">
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">End
                                                    Date <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="end_date" name="spcm_edat"
                                                           class="form-control col-md-7 col-xs-12 in_tg date"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           value="{{ date('Y-m-d')}}" required="required" type="text">
                                                </div>
                                            </div>
{{--                                            <div class="item form-group">--}}
{{--                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name"> Select Qualifier <span--}}
{{--                                                            class="required">*</span>--}}
{{--                                                </label>--}}
{{--                                                <div class="col-md-6 col-sm-6 col-xs-12">--}}
{{--                                                    <select class="form-control cmn_select2" name="spcm_qyfr"--}}
{{--                                                            id="spcm_qyfr" required="required"--}}
{{--                                                    >--}}
{{--                                                        <option value="">Select Qualifier</option>--}}
{{--                                                        <option value="1">Value</option>--}}
{{--                                                        <option value="2">FOC</option>--}}

{{--                                                    </select>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
                                           <div class="item form-group">
                                                <div class="col-md-9 col-sm-9">
                                                   <button type="submit" class="btn btn-primary" style="float:right;">Save</button>
                                                </div>
                                           </div>
                                        </div>                                    
                                </form>
                                <div class="form-horizontal form-label-left dfim" id="dfim">

                                    <div class="animate__animated animate__zoomIn" >
                                        {{-- Information of Space Maintain --}}
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                   for="slgp_id">Select Group<span
                                                        class="required"></span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control cmn_select2" name="slgp_id" onchange="getItemsSpaces()"
                                                        id="slgp-sgit">

                                                    <option value="">Select Group</option>
                                                    @foreach($acmp as $acmpList)
                                                        <option value="{{$acmpList->slgp_id}}">{{$acmpList->slgp_code}}
                                                            - {{$acmpList->slgp_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Select Showcase and Offer--}}
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="amim_id"> Select Space
                                                <span class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control cmn_select2" onchange="showAmountOrItem()" name="spcm_id" id="spcm_id">
                                                    <option value="">Select Space</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Select Type<span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control cmn_select2" name="spcm_type" id="show-type">
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Search Item--}}
{{--                                        <div class="item form-group">--}}
{{--                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cat_id">--}}
{{--                                                Find Item--}}
{{--                                            </label>--}}
{{--                                            <div class="col-md-5 col-sm-5 col-xs-12">--}}
{{--                                                <input id="search_text" name="search_text" class="form-control col-md-7 col-xs-12 in_tg"--}}
{{--                                                    placeholder="Item name or Code"--}}
{{--                                                    type="text" value="{{ old('search_text') }}">--}}
{{--                                            </div>--}}
{{--                                            <div class="col-md-1 col-sm-1 col-xs-12">--}}
{{--                                                <button class="btn btn-success" onclick="loadItem()" style="float:right">Load Item</button>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}

                                        {{-- Item Dropdown --}}
                                        <div class="item form-group" >
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="amim_id"> Item
                                                <span class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control cmn_select2" onchange="getItemsInfos()" name="amim_id" id="amim_id">
                                                    <option value="">Select Item</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="item form-group" id="space-maintain-button">
                                            <div class="col-md-9 col-sm-9">
                                                <button type="submit" class="btn btn-primary" style="float:right;">Save</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- check -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item Mapping -->
                <div class="col-md-12 col-sm-12 col-xs-12 spsb-type-select" style="padding: 0">
                    <div class="x_panel" style="display: flex;
                            justify-content: center;
                            flex-direction: column;
                            align-items: center;">
                        <div class="x_title text-center" style="width: 100%">

                            <button id="send" onclick="storeShowcaseInfo()"
                                        type="submit" class="btn btn-success">Save</button>
                            <div class="clearfix"></div>
                        </div>

                        <form class="form-horizontal form-label-left col-md-12 col-sm-12" id="showcase-form"
                              method="post" enctype="multipart/form-data">
                              {{csrf_field()}}


                            <div id="spsb-type-showcase-items">
                                <h4 class="text-center">Showcase Items</h4>
                                <table class="table" id="spsb-type-showcase-items-table"
                                       style="width: 100%; padding-top: 10px; padding-left: 5px">
                                    <thead>
                                    <tr class="tbl_header_light">
                                        <th>Item</th>
                                        <th style="width: 20%">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="showcase-items-list">

                                    </tbody>
                                </table>
                            </div>

{{--                            Free Items--}}
                            <div id="spsb-type-offer-items" class="col-md-12 col-sm-12" style="padding-left: 0px">
                                <h4 class="text-center">Offer</h4>
                                <div id="spsb-type-offer-items-table" class="col-md-5 col-sm-5" style="padding-left: 0px">

                                    <table class="table" id="spsb-type-offer-items-table"
                                           style="width: 100%; padding-top: 10px; padding-left: 5px" >
                                        <thead>
                                        <tr class="tbl_header_light">
                                            <th>Item</th>
                                            <th style="width: 20%">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody id="offer-item-list">

                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-2 col-sm-2 col-xs-12">
                                            <a id="radio" onclick="selectNationality('spft')">
                                                <input type="radio" checked="checked" value="1" id="spft_id_national"
                                                       name="spft_is_national"
                                                       onchange="showPromotionArea(this.value, 'spft');"> Nationally<br/>
                                            </a>

                                            <a id="radio" onclick="selectZonal('spft')">
                                                <input type="radio" value="0" id="spft_id_zonal"
                                                       name="spft_is_national"
                                                       onchange="showPromotionArea(this.value, 'spft');"> Zonal
                                            </a>
                                </div>
                                <div class="col-md-5 col-sm-5 col-xs-12">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12 spft-zone" for="name">Select
                                            Zone
                                        </label>
                                        <div class="col-md-7 col-sm-7 col-xs-12 spft-zone">
                                            <select class="form-control cmn_select2 in_tg" name="spft_zone_ids[]" id="area_item"
                                                    multiple="multiple">
                                                <option value="">Select Zone</option>
                                                @foreach ($zones as $zone)
                                                    <option value="{{ $zone->zone_id }}">{{ $zone->zone_code.' - '.$zone->zone_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-sm-2">
                                            <button type="button" onclick="storeFreeItemInfo()" class="btn btn-success" style="float:right; padding: 3px 7px;">
                                                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

{{--                            Free Amount --}}
                            <div id="spsb-type-values" class="col-md-12 col-sm-12" style="padding-left: 0">
                                <div class="item form-group">
                                    <h4 class="text-center">Amount</h4>
                                    <div class="col-md-5 col-sm-5 col-xs-12" style="padding-left: 0">
                                        <input id="spft_amnt" class="form-control col-md-7 col-xs-12 in_tg"
                                               name="spft_amnt"
                                               placeholder="Amount" type="number">
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-12">
                                        <a id="radio" onclick="selectNationality('spam')">
                                            <input type="radio" checked="checked" value="1" id="spam_id_national"
                                                   name="spam_is_national"
                                                   onchange="showPromotionArea(this.value, 'spam');"> Nationally <br/>
                                        </a>

                                        <a id="radio" onclick="selectZonal('spam')">
                                            <input type="radio" value="0" id="spam_id_zonal"
                                                   name="spam_is_national"
                                                   onchange="showPromotionArea(this.value, 'spam');"> Zonal
                                        </a>
                                    </div>
                                    <div class="col-md-5 col-sm-5 col-xs-12 item form-group">
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12 spam-zone" for="name">Select
                                                Zone
                                            </label>
                                            <div class="col-md-7 col-sm-7 col-xs-12 spam-zone">
                                                <select class="form-control cmn_select2 in_tg" name="spam_zone_ids[]" id="area_item"
                                                        multiple="multiple">

                                                    <option value="">Select Zone</option>
                                                    @foreach ($zones as $zone)
                                                        <option value="{{ $zone->zone_id }}">{{ $zone->zone_code.' - '.$zone->zone_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2 col-sm-2">
                                                <button type="button" onclick="storeFreeAmountInfo()" class="btn btn-success" style="float:right; padding: 3px 7px;">
                                                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ln_solid"></div>
                        </form>

                    </div>
                </div>

                <!-- Zone Mapping -->
                <div class="col-md-12 col-sm-12 col-xs-12 spaz-mapping" style="padding: 0">
                    <div class="x_panel" style="display: flex;
                            justify-content: center;
                            flex-direction: column;
                            align-items: center;">

                        <div class="x_content">

                            <form class="form-horizontal form-label-left" id="space-mapping-form" action="{{URL::to('spaceZoneMapping')}}"
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">

                                {{csrf_field()}}


                                <div class="animate__animated animate__zoomIn" >
                                    {{-- Select Group for Zone Map --}}
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                               for="slgp_id">Select Group<span
                                                    class="required"></span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control cmn_select2"  onchange="getMappingSpaces('slgp-space-mapping')"
                                                    id="slgp-space-mapping" required="required">

                                                <option value="">Select Group</option>
                                                @foreach($acmp as $acmpList)
                                                    <option value="{{$acmpList->slgp_id}}">{{$acmpList->slgp_code}}
                                                        - {{$acmpList->slgp_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Select Space --}}
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="space_id"> Select Space
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control cmn_select2" name="spcm_id" id="space-id" required="required">
                                                <option value="">Select Space</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">
                                            Promotion Type
                                            <span class="required">*</span>
                                        </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <a id="radio" onclick="selectNationality()">
                                                    <input type="radio" checked="checked" value="1" id="id_national"
                                                           name="is_national"
                                                           onchange="showPromotionArea(this.value);"> Nationally &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>
                                                </a>

                                                <a id="radio" onclick="selectZonal()">
                                                    <input type="radio" value="0" id="id_zonal"
                                                           name="is_national"
                                                           onchange="showPromotionArea(this.value);"> Zonal &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </a>
                                            </div>
                                    </div>



                                    <div class="item form-group" id="myDiv" >
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Select
                                            Zone
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control cmn_select2 in_tg" name="zone_ids[]" id="area_item"
                                                    multiple="multiple">

                                                <option value="">Select Zone</option>
                                                @foreach ($zones as $zone)
                                                    <option value="{{ $zone->zone_id }}">{{ $zone->zone_code.' - '.$zone->zone_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="item form-group" id="space-maintain-button">
                                        <div class="col-md-9 col-sm-9">
                                            <button type="submit" class="btn btn-primary" style="float:right;">Save</button>
                                        </div>
                                    </div>
                                </div>


                                <div class="ln_solid"></div>
                            </form>

                        </div>
                    </div>
                </div>

                <!-- Site Mapping -->
                <div class="col-md-12 col-sm-12 col-xs-12 spst-mapping" style="padding: 0">
                    <div class="x_panel" style="display: flex;
                            justify-content: center;
                            flex-direction: column;
                            align-items: center;">

                        <div class="x_content">

                            <form class="form-horizontal form-label-left" id="site-mapping-form" action="{{URL::to('spaceSiteMapping')}}"
                                  method="post" enctype="multipart/form-data">
                                  {{csrf_field()}}


                                <div class="animate__animated animate__zoomIn" >
                                    {{-- Select Group for Site Map --}}
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                               for="slgp_id">Select Group<span
                                                    class="required"></span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control cmn_select2" required="required"
                                                    onchange="getMappingSpaces('slgp-space-site-mapping','spst-space-id')"
                                                    id="slgp-space-site-mapping">

                                                <option value="">Select Group</option>
                                                @foreach($acmp as $acmpList)
                                                    <option value="{{$acmpList->slgp_id}}">{{$acmpList->slgp_code}}
                                                        - {{$acmpList->slgp_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Select Space --}}
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="spst_space_id"> Select Space
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control cmn_select2" name="spcm_id" id="spst-space-id" required="required">
                                                <option value="">Select Space</option>
                                            </select>
                                        </div>
                                    </div>



                                    <div class="item form-group" id="myDiv">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="site-id">Select
                                            Site
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="site-code" name="site_code" class="form-control col-md-7 col-xs-12 in_tg"
                                                        placeholder="Site Code" required
                                                        type="text" value="{{ old('site_code') }}">
                                            </div>
                                        </div>


                                        <div class="item form-group" id="site-mapping-button">
                                            <div class="col-md-9 col-sm-9">
                                                <button type="submit" class="btn btn-primary" style="float:right;">Save</button>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="ln_solid"></div>
                                </form>

                            </div>
                        </div>
                    </div>

                <!-- Site Mapping Bulk Upload-->
                <div class="col-md-12 col-sm-12 col-xs-12 spst-mapping" style="padding-left: 0; padding-right: 0;">
                    <div class="x_panel">
                        <div class="x_title">
                            <h4 class ="text-center">Site Mapping</h4>

                            <a class="btn btn-success btn-sm" href="{{url('space-site-mapping-format')}}">Download Format </a>

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
                            <form class="form-horizontal form-label-left" action="{{URL::to('space-site-mapping-upload')}}"
                                  method="post" enctype="multipart/form-data">
                                {{csrf_field()}}


                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Choose File<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="space_site_file" class="form-control col-md-7 col-xs-12"
                                               name="space_site_file" type="file"
                                               step="1">
                                    </div>
                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button id="send" type="submit" class="btn btn-success">Submit</button>
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
            .fa-times-circle-o:hover{
                cursor: pointer;
            }

            #radio:hover{
                color: #73879C;
                cursor: pointer;
            }
        </style>
        <script>
            $(document).ready(function (){


                $('.date').datepicker({
                    dateFormat: 'yy-mm-dd',
                    autoclose: 1,
                    showOnFocus: true
                });

            })


            function showPromotionArea(type, area=null) {
                if (type == '0' && area ===null) {
                    $('#myDiv').show();
                } else if(type == '1' && area ===null){
                    $('#myDiv').hide();
                } else if(type == '1' && area === 'spft'){
                    $('.spft-zone').hide();
                } else if(type == '0' && area === 'spft'){
                    $('.spft-zone').show();
                } else if(type == '0' && area === 'spam'){
                    $('.spam-zone').show();
                } else if(type == '1' && area === 'spam'){
                    $('.spam-zone').hide();
                }
            }

            $('#myDiv').hide();
            $('.spam-zone').hide();
            $('.spft-zone').hide();
            $("#slgp_id").select2();
            $(".cmn_select2").select2();

            function hide(){
                $('#spcm').hide();
                $('.dfim').hide();
                $('.spaz-mapping').hide();
                $('.spsb-type-select').hide();
                $('.spst-mapping').hide();
            }

            hide();

            $('#spcm').show();

            function showSpaceMaintain(){
                hide();
                $('.spcm').show();
                $('#space-maintain-button').show();
            }

            function showShowcaseArea(){
                hide();
                $('#space-maintain-button').hide();
                $('.spsb-type-select').show();
                $('.dfim').show();
            }

            function getSpaceArea(){
                hide();
                $('.spaz-mapping').show();
            }

            function getSiteMapping(){
                hide();
                $('.spst-mapping').show();
            }

            function loadItem(){
                var search_text=$('#search_text').val();
                if(search_text.length>2){
                    $('#ajax_load').css('display','block');
                    $.ajax({
                        type:"GET",
                        url:"{{URL::to('/')}}/loadItem/"+search_text,
                        cache:"false",
                        success:function(data){
                            $('#ajax_load').css('display','none');
                            $('#amim_id').empty();
                            var html='<option value="" disabled>Select Item</option>';
                            if(data){
                                for(var i=0;i<data.length;i++){
                                    html+='<option value="'+data[i].id+'">'+data[i].amim_code+'-'+data[i].amim_name+'</option>';
                                }
                            }

                            $('#amim_id').append(html);

                        },error:function(error){
                            $('#ajax_load').css('display','none');
                        }
                    });
                }
                else{
                    Swal.fire({
                        text: 'Search Text Length Should be More Than Two!',
                    })
                }
            }

            function showTypes(){
                let type = $('#spcm_type').select2('data')[0]

                if(type.id === '2')
                {
                    $('.spsb').show();
                }
            }

            function getItemsSpaces(){
                let slgp = $('#slgp-sgit').select2('data')[0]

                var _token=$('#_token').val();
                let slgp_id = slgp.id

                // slgp items by slgp id
                if(slgp_id !=''){
                    $.ajax({
                        type:"POST",
                        url:"{{URL::to('/')}}/itemBySlgpId",
                        data:{
                            slgp_id:slgp_id,
                            _token:_token,
                        },
                        cache:"false",
                        success:function(data){
                            $('#ajax_load').css('display','none');
                            $('#amim_id').empty();
                            var html='<option value="" disabled>Select Item</option>';
                            if(data){
                                for(var i=0;i<data.length;i++){
                                    html+='<option value="'+data[i].amim_id+'">'+data[i].amim_code+'-'+data[i].amim_name+'</option>';
                                }
                            }
                            $('#amim_id').append(html);
                        },error:function(error){
                            $('#ajax_load').css('display','none');
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: 'No Items Found with this Sale Group ID',
                            });
                        }
                    });
                }
                else{
                    Swal.fire({
                        title: 'Failed',
                        text: 'Please fill all the required input field!',
                    });
                }

                // maintain space by slgp id
                if(slgp_id !=''){
                    $.ajax({
                        type:"POST",
                        url:"{{URL::to('/')}}/spaceMaintainBySlgpId",
                        data:{
                            slgp_id:slgp_id,
                            _token:_token,
                        },
                        cache:"false",
                        success:function(data){
                            $('#ajax_load').css('display','none');
                            $('#spcm_id').empty();
                            var html='<option value="" selected disabled>Select Space</option>';
                            if(data){
                                for(var i=0;i<data.length;i++){
                                    html+='<option value="'+data[i].id+'">'+data[i].spcm_code+'-'+data[i].spcm_name+'</option>';
                                }
                            }
                            $('#spcm_id').append(html);
                        },error:function(error){
                            $('#ajax_load').css('display','none');
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: 'No Space Found with this Sale Group ID',
                            });
                        }
                    });
                }
                else{
                    Swal.fire({
                        title: 'Failed',
                        text: 'Please fill all the required input field!',
                    });
                }
            }

            function showAmountOrItem(){
                $('#show-type').empty();
                let spcm = $('#spcm_id').select2('data')[0]
                let spcm_id = spcm.id
                var _token=$('#_token').val();

                if(spcm.id) {
                    $('#spsb-type-showcase-items').append(`
                        <input type="hidden" name="spcm_id" value="${spcm_id}"/>
                    `);
                }

                // get selected space info
                if(slgp_id !=''){
                    $.ajax({
                        type:"POST",
                        url:"{{URL::to('/')}}/getSpaceMaintainInfo",
                        data:{
                            spcm_id:spcm_id,
                            _token:_token,
                        },
                        cache:"false",
                        success:function(data){
                            $('#ajax_load').css('display','none');

                            $('#show-type').empty();
                            if(data[0]){
                                let selected
                                var html='<option value="" disabled>Select Option</option>';
                                let info = data[0]
                                html+=` <option value="1">Showcase Items</option>
                                        <option value="3">Offer Item</option>`;

                                $('#spsb-type-showcase-items').show();
                                $('#offer-item-list').html('');
                                $('#showcase-items-list').empty();
                                $('#spsb-type-values').show();
                                $('#spsb-type-showcase-items').show();
                                $('#spsb-type-offer-items').show();
                            }
                            $('#show-type').append(html);
                        },error:function(error){
                            $('#ajax_load').css('display','none');
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: 'No Space Found with this Sale Group ID',
                            });
                        }
                    });
                }
                else{
                    Swal.fire({
                        title: 'Failed',
                        text: 'Please fill all the required input field!',
                    });
                }
            }
            //
            // function getTypeValues(){
            //     let type = $('#show-type').select2('data')[0]
            //     let type_id = type.id
            //
            //     if(type_id === '1'){
            //         $(".spsb-type-select").show()
            //
            //         let valuesFields = ``
            //
            //         $('#spsb-type-offer-items').append(valuesFields)
            //
            //     }else if(type_id === '2'){
            //         $(".spsb-type-select").show()
            //
            //         let valuesFields = ``
            //
            //         $('#spsb-type-values').append(valuesFields)
            //     }else{
            //         $(".spsb-type-select").show()
            //
            //         let valuesFields = ``
            //
            //         $('#spsb-type-showcase-items').append(valuesFields)
            //     }
            // }

            function getItemsInfos(){
                let items = $('#amim_id').select2('data')[0]

                let type = $('#show-type').select2('data')[0]

                let spsb_id_exist = true;

                $('.spsb-id').each(function() {
                    if($(this).val() === items.id)
                    {
                        spsb_id_exist = false
                    }
                })

                let spft_id_exist = true;

                $('.spft-id').each(function() {
                    if($(this).val() === items.id)
                    {
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
                            ${items.text}
                            <input type="hidden" value="${items.id}" class="spft-id" name="spft_ids[]">
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
                            ${items.text}
                            <input type="hidden" value="${items.id}" class="spsb-id" name="spsb_ids[]">
                        </td>
                        <td>
                            <i class="fa fa-times-circle-o text-danger" aria-hidden="true" onclick="deleteRow(this)"></i>
                        </td>
                    </tr>`)
                }
            }

            // store showcase information
            {{--function storeShowcaseInformation(){--}}
            {{--    let values = $('#showcase-form').serialize();--}}

            {{--    if(slgp_id !=''){--}}
            {{--        $.ajax({--}}
            {{--            type:"POST",--}}
            {{--            url:"{{URL::to('/')}}/updateShowcaseItems",--}}
            {{--            data: values,--}}
            {{--            cache:"false",--}}
            {{--            success:function(data){--}}
            {{--                $('#ajax_load').css('display','none');--}}

            {{--                swal.fire({--}}
            {{--                    icon: 'success',--}}
            {{--                    title: 'Showcase Information Updated',--}}
            {{--                })--}}

            {{--                clearShowcaseFields()--}}
            {{--            },error:function(error){--}}
            {{--                $('#ajax_load').css('display','none');--}}
            {{--                Swal.fire({--}}
            {{--                    icon: 'error',--}}
            {{--                    title: 'Failed',--}}
            {{--                    text: 'Wrong input values',--}}
            {{--                });--}}
            {{--            }--}}
            {{--        });--}}
            {{--    }--}}
            {{--    else{--}}
            {{--        Swal.fire({--}}
            {{--            title: 'Failed',--}}
            {{--            text: 'Please fill all the required input field!',--}}
            {{--        });--}}
            {{--    }--}}
            {{--}--}}

            function deleteRow(that){
                $(that).attr('disabled', 'disabled');
                $(that).parent().parent().remove();
            }

            function getMappingSpaces(source_id, target_id = ''){

                let slgp = $(`#${source_id}`).select2('data')[0]

                var _token=$('#_token').val();
                let slgp_id = slgp.id


                // maintain space by slgp id
                if(slgp_id !=''){
                    $.ajax({
                        type:"POST",
                        url:"{{URL::to('/')}}/spaceMaintainBySlgpId",
                        data:{
                            slgp_id:slgp_id,
                            _token:_token,
                        },
                        cache:"false",
                        success:function(data){
                            $('#ajax_load').css('display','none');

                            let html = '<option value="" selected disabled>Select Space</option>';
                            if (data) {
                                for (var i = 0; i < data.length; i++) {
                                    html += '<option value="' + data[i].id + '">' + data[i].spcm_code + '-' + data[i].spcm_name + '</option>';
                                }
                            }


                            if(target_id === 'spst-space-id'){
                                $('#spst-space-id').empty();
                                $('#spst-space-id').append(html);
                            }else{
                                $('#space-id').empty();
                                $('#space-id').append(html);
                            }
                        },error:function(error){
                            $('#ajax_load').css('display','none');
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: 'No Space Found with this Sale Group ID',
                            });
                        }
                    });
                }
                else{
                    Swal.fire({
                        title: 'Failed',
                        text: 'Please fill all the required input field!',
                    });
                }
            }

            function selectNationality(area=null){
                if(area === null) {
                    $('#id_national').prop('checked', true);
                }else{
                    console.log(`${area}_id_national`)
                    $(`#${area}_id_national`).prop('checked', true);
                }

                showPromotionArea('1', area);
            }

            function selectZonal(area=null){
                if(area === null) {
                    $('#id_zonal').prop('checked', true);
                }else{
                    console.log(`#${area}_id_zonal`)
                    $(`#${area}_id_zonal`).prop('checked', true);
                }
                showPromotionArea('0', area);
            }

            function clearShowcaseFields(){
                $('#spft_amnt').val('');
                $('#offer-item-list').empty();
                $('#showcase-items-list').empty();
            }

            function storeShowcaseInfo(){
                let info = $('#showcase-form').serialize()
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/updateShowcaseItems",
                    data: info,
                    cache:"false",
                    success:function(data){
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `${data.success}`,
                        });
                    },error: (error) => {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Failed',
                            text: error.responseJSON.error,
                        });
                    }
                });
            }

            function storeFreeItemInfo(){
                let info = $('#showcase-form').serialize()
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/updateFreeItems",
                    data: info,
                    cache:"false",
                    success:function(data){
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `${data.success}`,
                        });
                    },error: (error) => {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Failed',
                            text: error.responseJSON.error,
                        });
                    }
                });
            }

            function storeFreeAmountInfo(){
                let info = $('#showcase-form').serialize()
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/updateFreeAmount",
                    data: info,
                    cache:"false",
                    success:function(data){
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `${data.success}`,
                        });
                    },error: (error) => {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Failed',
                            text: error.responseJSON.error,
                        });
                    }
                });
            }
        </script>
    @endsection