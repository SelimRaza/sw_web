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
                            <a href="{{ URL::to('/trip')}}">All Trip</a>
                        </li>
                        <li class="active">
                            <strong>Trip Product</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>


            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12">
                    <form id="demo-form2" data-parsley-validate
                          class="form-horizontal form-label-left"
                          action="{{ URL::to('/trip/orderEdit/'.$trip->id)}}" enctype="multipart/form-data"
                          method="post">
                        {{csrf_field()}}
                        {{method_field('POST')}}
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
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
                                    <div class="x_title">
                                        <h1>Order Edit</h1>

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
                                        <div class="col-md-1 col-sm-1 col-xs-12">

                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">

                                        <table class="table table-striped projects">
                                            <thead>
                                            <tr>
                                                <th>S/L</th>
                                                <th>Order Id</th>
                                                <th>Order Date</th>
                                                <th>Salesman</th>
                                                <th>Site Name</th>
                                                <th>Item Name</th>
                                                <th>Item Code</th>
                                                <th>order Qty</th>
                                                <th>Confirm Qty</th>
                                                <th>Stock Qty</th>

                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $order_id=""?>
                                            @foreach($tripOrder as $index => $tripOrder1)
                                                {{--  t2.ordm_ornm as order_id,
                                                  t8.aemp_usnm as  sr_id,
                                                  t9.optp_name as  payment_type,
                                                  t6.site_code as site_code,
                                                  t6.site_name as  site_name,
                                                  t2.ordm_date as order_date,
                                                  t3.id,
                                                  t4.amim_code as sku_code,
                                                  t3.ordd_uprc as unit_price,
                                                  t4.amim_name as item_name,
                                                  t3.ordd_qnty as order_qty,
                                                  t3.ordd_qnty/t3.ordd_duft as order_ctn_qty,
                                                  t5.dlst_qnty as stock_qty,
                                                  t3.ordd_duft as ctn_size,
                                                  t3.ordd_dfdo as defult_discount,
                                                  t3.ordd_spdo as sp_dis,
                                                  t3.ordd_opds as promo_dis,
                                                  t3.ordd_oamt as order_amouont,
                                                  t3.prom_id as promo_ref--}}
                                                <tr>
                                                    <td>{{$index+1}}</td>
                                                    <?php if($order_id!=$tripOrder1->order_id){?>
                                                    <td>Order Id: {{$tripOrder1->order_id}}
                                                    </td>
                                                    <td>Order Id: {{$tripOrder1->order_date}}
                                                    </td>
                                                    <td>Order Id: {{$tripOrder1->sr_id}}
                                                    </td>
                                                    <td>Order Id: {{$tripOrder1->site_name}}
                                                    </td>
                                                    <?php
                                                    $order_id =$tripOrder1->order_id;}else{?>
                                                    <td>
                                                    </td>
                                                    <td>
                                                    </td>
                                                    <td>
                                                    </td>
                                                    <td>
                                                    </td>
                                                     <?php
                                                        }?>
                                                    <td><input type="hidden" name="rtdd_id[]" value="{{$tripOrder1->id}}">Item: {{$tripOrder1->item_name}}
                                                    </td>
                                                    <td>Item: {{$tripOrder1->sku_code}}
                                                       <input
                                                                type="hidden" name="ctn_size[]"
                                                                value="{{$tripOrder1->ctn_size}}"><input
                                                                type="hidden" name="order_qty[]"
                                                                value="{{$tripOrder1->order_qty}}"></td>
                                                    <td style="color: <?php if ($tripOrder1->stock_qty  < $tripOrder1->order_qty  || $tripOrder1->stock_qty  < 0) {
                                                        echo "red";
                                                    }?>">{{$tripOrder1->order_ctn_qty}}</td>
                                                    <td style="color: <?php if ($tripOrder1->is_free_item ==1) {
                                                        echo "red";
                                                    }?>">O Qty: <input type="number" min="0"
                                                                       max="<?php if ($tripOrder1->stock_qty > 0) {
                                                                           echo $tripOrder1->stock_qty/ $tripOrder1->ctn_size;
                                                                       } else {
                                                                           echo 0;
                                                                       }?>" step="any" name="p_qty[]"
                                                                       value="{{$tripOrder1->order_ctn_qty}}">
                                                        <input type="hidden" name="qty_rate[]"
                                                               value="{{$tripOrder1->unit_price}}"><input type="hidden"
                                                                                                          name="def_discount[]"
                                                                                                          value="{{$tripOrder1->defult_discount}}"><input
                                                                type="hidden" name="discount[]"
                                                                value="{{$tripOrder1->sp_dis}}"><input
                                                                type="hidden" name="promo_discount[]"
                                                                value="{{$tripOrder1->promo_dis}}"></td>
                                                    <td>{{$tripOrder1->stock_qty/$tripOrder1->ctn_size}}</td>


                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2>Trip{{$trip->id}}
                                            <small>{{$trip->depot()->name}}</small>
                                        </h2>
                                        <ul class="nav navbar-right panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                                                   role="button"
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
                                        <br/>

                                        <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                   for="first-name">Trip
                                                <span class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="mobile_2" name="mobile_2"
                                                       class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       placeholder="Mobile 2" type="text" value="{{$trip->id}}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                   for="first-name">Emp Name
                                                <span class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="mobile_2" name="mobile_2"
                                                       class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       placeholder="Mobile 2" type="text"
                                                       value="{{$trip->employee()->aemp_usnm.'-'.$trip->employee()->aemp_name}}">
                                            </div>
                                        </div>
                                        <div class="ln_solid"></div>
                                        <div class="form-group">
                                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                                <button type="submit" class="btn btn-success">Next</button>
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
    <script type="text/javascript">


    </script>
@endsection