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


                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="item form-group">
                                        <table id="myTableSlab"
                                               class="table table-bordered table-striped projects">
                                            <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Slab Text</th>
                                                <th>Minimum Item Quantity</th>
                                                <th>Free Item Quantity</th>
                                                <th>Minimum Amount</th>
                                                <th>Discount Amount</th>
                                                <th>Slab Type</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($slab_sql as $index => $slab_sql)
                                                <tr>
                                                    <td>{{$index+1}}</td>
                                                    <td>{{$slab_sql->prsb_text}}</td>
                                                    <td>{{$slab_sql->slab_min_qnty}}</td>
                                                    <td>{{$slab_sql->offer_qnty}}</td>
                                                    <td>{{$slab_sql->slab_min_amnt}}</td>
                                                    <td>{{$slab_sql->offer_amnt}}</td>
                                                    <td>{{$slab_sql->slab_type}}</td>
                                                </tr>
                                            @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <hr/>
                                <strong>
                                    <center> ::: Buy item :::</center>
                                </strong>
                                <hr/>

                                <div class="item form-group">

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <table id="myTableSlab"
                                               class="table table-bordered table-striped projects">
                                            <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Item Code</th>
                                                <th>Item Name</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($buy_item as $index => $buy_item)
                                                <tr>
                                                    <td>{{$index+1}}</td>
                                                    <td>{{$buy_item->amim_code}}</td>
                                                    <td>{{$buy_item->amim_name}}</td>
                                                </tr>
                                            @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                                <hr/>
                                <strong>

                                    <center> ::: Free item :::</center>
                                </strong>
                                <hr/>

                                <div class="item form-group">

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <table id="datatable"
                                               class="table table-bordered table-striped projects">
                                            <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Item Code</th>
                                                <th>Item Name</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($free_item as $index => $free_item)
                                                <tr>
                                                    <td>{{$index+1}}</td>
                                                    <td>{{$free_item->amim_code}}</td>
                                                    <td>{{$free_item->amim_name}}</td>
                                                </tr>
                                            @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                                <hr/>
                                <strong>
                                    <center> ::: Assign Area :::</center>
                                </strong>
                                <hr/>
                                <div class="item form-group">

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div><a href="{{ URL::to('export/promotion/site',$prmr_id)}}" class="btn btn-success" style="float:right;margin-bottom:15px;">Export </a></div>
                                        <table id="prsm_site"
                                               class="table table-bordered table-striped projects">
                                            <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Site Code</th>
                                                <th>Site Name</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($site_code as $index => $st)
                                                <tr>
                                                    <td>{{($site_code->perPage() * ($site_code->currentPage() - 1)) +$index+ 1}}</td>
                                                    <td>{{$st->site_code}}</td>
                                                    <td>{{$st->site_name}}</td>
                                                </tr>
                                            @endforeach

                                            </tbody>
                                        </table>
                                        {!! $site_code->links() !!}
                                    </div>
                                </div>

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
        function clearPrice() {
            $('#f_item_qty').val("");
            $('#f_item_price').val("");
        }

        function showFreeItemPrice(f_id) {
            clearPrice();
            var _token = $("#_token").val();
            var slgp_id = $("#slgp_id").val();
            if (f_id) {
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
        function calcDisc() {
            var min = parseInt($('#mi_qty').val());
            var max = parseInt($('#max_qty').val());
            if (Number.isInteger(min) && Number.isInteger(max)) {
                let disc = ((min / max) * 100).toFixed(2);
                $('#dis_percen').val(disc);
            }
            else {
                $('#dis_percen').val(0);
            }
        }


    </script>
@endsection