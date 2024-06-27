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
                          action="{{ URL::to('/trip/productAssign/'.$trip->id)}}" enctype="multipart/form-data"
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
                                        <h1>Order Item Summary</h1>

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
                                                <th>SKU Id</th>
                                                <th>SKU Name</th>
                                                <th>SKU Code</th>
                                                <th>Stock QTY</th>
                                                <th>QTY</th>
                                                <th>Load QTY</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($tripOrderSKu as $index => $tripOrderSKu1)

                                                <tr>
                                                    <td>{{$index+1}}</td>
                                                    <td><input readonly id="name"
                                                               class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6"
                                                               data-validate-words="2" name="trip_order_sku[]"
                                                               value="{{$tripOrderSKu1->sku_id}}"
                                                               placeholder="Name" required="required" type="text">
                                                    </td>

                                                    <td @if($tripOrderSKu1->stock_qty<$tripOrderSKu1->qty_order)
                                                        style="color:red"
                                                            @endif
                                                    > {{$tripOrderSKu1->sku_name}}</td>
                                                    <td>{{$tripOrderSKu1->sku_code}}</td>
                                                    <td>{{$tripOrderSKu1->stock_qty}}</td>
                                                    <td><input readonly id="name"
                                                               class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6"
                                                               data-validate-words="2"
                                                               value="{{$tripOrderSKu1->qty_order}}"
                                                               name="trip_order_sku_qty[]"
                                                               placeholder="Name" required="required" type="text">
                                                    </td>
                                                    <td><input id="name"
                                                               class="form-control col-md-7 col-xs-12"

                                                               data-validate-length-range="6"
                                                               max="{{$tripOrderSKu1->stock_qty}}" min="0"
                                                               data-validate-words="2" name="trip_sku_qty[]"
                                                               value="{{$tripOrderSKu1->qty_order}}"
                                                               placeholder="0" required="required" type="number">
                                                    </td>

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
                                        <h1>GRV Item Summary</h1>

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
                                                <th>SKU Id</th>
                                                <th>SKU Name</th>
                                                <th>SKU Code</th>
                                                <th>QTY</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($tripGrvSKu as $index => $tripGrvSKu1)
                                                <tr>
                                                    <td>{{$index+1}}</td>
                                                    <td><input readonly id="name"
                                                               class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               name="trip_grv_sku[]"
                                                               value="{{$tripGrvSKu1->sku_id}}"
                                                               placeholder="Name" required="required" type="text"></td>
                                                    <td>{{$tripGrvSKu1->sku_name}}</td>
                                                    <td>{{$tripGrvSKu1->sku_code}}</td>
                                                    <td><input readonly id="name"
                                                               class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               name="trip_grv_sku_qty[]"
                                                               value="{{$tripGrvSKu1->qty_order}}"
                                                               placeholder="Name" required="required" type="text"></td>

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
                                        <h2>{{$trip->id}}
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