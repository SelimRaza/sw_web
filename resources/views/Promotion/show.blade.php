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
                            <strong>Show Promotion</strong>
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
                            <form class="form-horizontal form-label-left"
                                  action="{{route('promotion.create.exist')}}"
                                  method="post">
				 <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}
                                <strong>
                                    <center> ::: Promotion Details :::</center>
                                </strong>
                                <hr/>
                                @if($depot->lfcl_id==2)
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Promotion
                                        Name <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" name="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                              
                                               type="text">
                                        <input type="hidden" name="promp_id" value="{{$depot->id}}">
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
                                              
                                               type="text">
                                    </div>
                                </div>
                                @else
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Promotion
                                        Name <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" name="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               type="text" value="{{$depot->prom_name}}">
                                        <input type="hidden" name="promp_id" value="{{$depot->id}}">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Promotion
                                        Code <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="Code" name="Code" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               type="text" value="{{$depot->prom_code}}">
                                    </div>
                                </div>
                                @endif
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Start
                                        Date <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="startDate" name="startDate"
                                               class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               value="{{$depot->prom_sdat}}"  type="text">
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
                                               value="{{$depot->prom_edat}}"  type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Sales
                                        Group
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="slgp_id" id="slgp_id"
                                                required onchange="filterItem(this.value)">

                                            <option value="{{$depot->slgp_id}}">{{$depot->slgp_name}}</option>

                                            @foreach ($salesGroups as $salesGroups)
                                                @if($depot->slgp_id !=$salesGroups->slgp_id)
                                                <option value="{{ $salesGroups->slgp_id }}">{{ $salesGroups->slgp_code.' - '.$salesGroups->slgp_name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <hr/>
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
                                                required>
                                            <option value="{{$depot->buy_item_id}}">{{$depot->buy_item_code}} - {{$depot->buy_item}}</option>
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
                                               value="{{$depot->item_m_qty}}" 
                                               type="number" onkeyup="calcDisc()">
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
                                               value="{{$depot->item_min_qty}}" 
                                               type="number" onkeyup="calcDisc()">
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

                                             <option value="{{$depot->item_id}}">{{$depot->free_item_code}} - {{$depot->free_item}}</option>
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
                                               value="{{$depot->free_item_qty}}" 
                                               type="number">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Free
                                        Item Price <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="f_item_price" name="f_item_price"
                                               class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               value="{{$depot->free_item_price}}" 
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
                                               value="{{$depot->discount_percentage}}" 
                                               type="text">
                                    </div>
                                </div>
                                <hr/>
                                <strong>
                                    <center> ::: Assign Area :::</center>
                                </strong>
                                <hr/>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Promotion
                                        Type<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                    @if($depot->promotionType=='Nationally')

                                        <input type="radio"  value="0" id="id_national" checked="checked" 
                                           name="pro_type"
                                           onchange="showChildQuestions(this.value);"> Nationally <br/>
                                        
                                    @else
                                    <input type="radio" value="1" id="id_zonal" checked="checked" 
                                           name="pro_type"
                                           onchange="showChildQuestions(this.value);"> Zonal
                                    </div>
                                    @endif
                                </div>
                               
                                @if($depot->lfcl_id==2)
                                <div id="add-promotion-div" class="col-md-6">
                                    <button class="btn btn-dark " type="submit" >Add New Promotion</button>
                                </div>
                                @endif
                                @if($depot->promotionType!='Nationally')

                                    <div class="x_content">
                                        <table id="datatable" class="table table-bordered table-striped projects"
                                               data-page-lentg="100">
                                            <thead>
                                            <tr class="tbl_header">
                                                <th>SL</th>
                                                <th>Zone Id</th>
                                                <th>Zone Name</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($zones as $index => $zone)
                                                <tr>
                                                    <td>{{$index+1}}</td>
                                                    <td>{{$zone->zone_name}}</td>
                                                    <td>{{$zone->zone_code}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>

                                    </div>
                                @endif

                                
                            </form>
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

        function showChildQuestions(type) {
            if (type == '1') {
                $('#myDiv').show();
            } else {
                $('#myDiv').hide();
            }
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
                    console.log(data);
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


    </script>
@endsection