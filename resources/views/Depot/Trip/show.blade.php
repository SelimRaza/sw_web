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
                            <strong>Show Trip</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>
            <div class="clearfix"></div>

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
                            <h1>Order Details</h1>

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
                                    <th>Order Amount</th>
                                    <th>Invoice Amount</th>
                                    <th>Order Date</th>
                                    <th>Order Date Time</th>
                                    <th>User Name</th>
                                    <th>Emp Name</th>
                                    <th>Outlet id</th>
                                    <th>Outlet Code</th>
                                    <th>Outlet Name</th>
                                    <th>Order Type</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($tripOrders as $index => $tripOrder)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$tripOrder->order_id}}</td>
                                        <td>{{$tripOrder->order_amount}}</td>
                                        <td>{{$tripOrder->invoice_amount}}</td>
                                        <td>{{$tripOrder->order_date}}</td>
                                        <td>{{$tripOrder->order_date_time}}</td>
                                        <td>{{$tripOrder->user_name}}</td>
                                        <td>{{$tripOrder->emp_name}}</td>
                                        <td>{{$tripOrder->site_id}}</td>
                                        <td>{{$tripOrder->site_code}}</td>
                                        <td>{{$tripOrder->site_name}}</td>
                                        <td>{{$tripOrder->order_type}}</td>
                                        <td>{{$tripOrder->status_name}}</td>
                                        @if($tripOrder->status_id==23)
                                            <td>
                                                @if($permission->wsmu_updt)
                                                    <form style="display:inline"
                                                          action="{{ URL::to('trip/tripStatusChange/'.$tripOrder->so_id.'/'.$trip->id)}}"
                                                          class="pull-xs-right5 card-link" method="POST">
                                                        {{csrf_field()}}
                                                        {{method_field('POST')}}
                                                        <input class="btn btn-danger btn-xs" type="submit"
                                                               value="Status Change"
                                                        >
                                                        </input>
                                                    </form>
                                                @endif
                                            </td>
                                        @endif
                                        @if($tripOrder->status_id==11)
                                            <td>
                                                <a target="_blank"
                                                   href="{{ URL::to('/printer/salesInvoice/'.$tripOrder->cont_id.'/'.$tripOrder->order_id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i>Sales Invoice
                                                    Print
                                                </a>

                                            </td>
                                        @endif

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
                            <h1>GRV Details</h1>

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
                                    <th>GRV Id</th>
                                    <th>GRV Amount</th>
                                    <th>Invoice Amount</th>
                                    <th>GRV Date</th>
                                    <th>GRV Date Time</th>
                                    <th>User Name</th>
                                    <th>Emp Name</th>
                                    <th>Outlet id</th>
                                    <th>Outlet Code</th>
                                    <th>Outlet Name</th>
                                    <th>GRV Type</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($tripGRvs as $index => $tripGRv)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$tripGRv->order_id}}</td>
                                        <td>{{$tripGRv->order_amount}}</td>
                                        <td>{{$tripGRv->invoice_amount}}</td>
                                        <td>{{$tripGRv->order_date}}</td>
                                        <td>{{$tripGRv->order_date_time}}</td>
                                        <td>{{$tripGRv->user_name}}</td>
                                        <td>{{$tripGRv->emp_name}}</td>
                                        <td>{{$tripGRv->site_id}}</td>
                                        <td>{{$tripGRv->site_code}}</td>
                                        <td>{{$tripGRv->site_name}}</td>
                                        <td>{{$tripGRv->order_type}}</td>
                                        <td>{{$tripGRv->status_name}}</td>
                                        @if($tripGRv->status_id==23)
                                            <td>
                                                @if($permission->wsmu_updt)
                                                    <form style="display:inline"
                                                          action="{{ URL::to('trip/tripGStatusChange/'.$tripGRv->so_id.'/'.$trip->id)}}"
                                                          class="pull-xs-right5 card-link" method="POST">
                                                        {{csrf_field()}}
                                                        {{method_field('POST')}}
                                                        <input class="btn btn-danger btn-xs" type="submit"
                                                               value="Status Change"
                                                        >
                                                        </input>
                                                    </form>
                                                @endif
                                            </td>
                                        @endif
                                        @if($tripGRv->status_id==11)
                                            <td>
                                                <a target="_blank"
                                                   href="{{ URL::to('/printer/returnInvoice/'.$tripGRv->cont_id.'/'.$tripGRv->order_id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i>Return
                                                    Invoice Print
                                                </a>


                                            </td>
                                        @endif

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
                            <h1>Move Request</h1>

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
                                    <th>Load Id</th>
                                    <th>Request Date</th>
                                    <th>Load Date</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($tripStockMove as $index => $tripStockMove1)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$tripStockMove1->load_code}}</td>
                                        <td>{{$tripStockMove1->request_date}}</td>
                                        <td>{{$tripStockMove1->load_date}}</td>
                                        <td>{{$tripStockMove1->type}}</td>
                                        <td>{{$tripStockMove1->status}}</td>
                                        <td><a href="{{ URL::to('/trip/loadProduct/'.$tripStockMove1->id)}}"
                                               class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> View</a>
                                        </td>
                                        <td>@if($permission->wsmu_updt)
                                                @if(($tripStockMove1->type_id==2 || $tripStockMove1->type_id==3 ||$tripStockMove1->type_id==5) && $tripStockMove1->status_id==23)
                                                    <a href="{{ URL::to('/trip/unloadProduct/'.$tripStockMove1->id)}}"
                                                       class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Unload
                                                        Verify</a>
                                                @endif
                                                @if($tripStockMove1->type_id==1 && $tripStockMove1->status_id==1)
                                                    <a href="{{ URL::to('/trip/loadProduct/'.$tripStockMove1->id)}}"
                                                       class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Load
                                                        Verify</a>
                                                @endif
                                            @endif</td>

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
                            <h1>Collection Details</h1>

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
                                    <th>Collection Id</th>
                                    <th>Collection Code</th>
                                    <th>Collection Amount</th>
                                    <th>Outlet Id</th>
                                    <th>Outlet Name</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($tripCollection as $index => $tripCollection1)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$tripCollection1->collection_id}}</td>
                                        <td>{{$tripCollection1->collection_code}}</td>
                                        <td>{{$tripCollection1->amount}}</td>
                                        <td>{{$tripCollection1->outlet_id}}</td>
                                        <td>{{$tripCollection1->outlet_name}}</td>

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
                    <form id="demo-form2" data-parsley-validate
                          class="form-horizontal form-label-left"
                          action="{{ URL::to('/trip/tripClose/'.$trip->id)}}" enctype="multipart/form-data"
                          method="post">
                        {{csrf_field()}}
                        {{method_field('POST')}}
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">

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
                                                <th>Issue QTY</th>
                                                <th>Confirm QTY</th>
                                                <th>Delivered QTY</th>
                                                <th>Logistic QTY</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($tripOrderSKu as $index => $tripOrderSKu1)
                                                <tr>
                                                    <td>{{$index+1}}</td>
                                                    <td><input readonly id="name"
                                                               class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               name="trip_sku[]"
                                                               value="{{$tripOrderSKu1->sku_id}}"
                                                               placeholder="Name" required="required" type="text"></td>
                                                    <td>{{$tripOrderSKu1->sku_name}}</td>
                                                    <td>{{$tripOrderSKu1->sku_code}}</td>
                                                    <td><input readonly id="name"
                                                               class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               value="{{$tripOrderSKu1->issued_qty}}"
                                                               placeholder="Name" required="required" type="text"></td>
                                                    <td><input readonly id="name"
                                                               class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               value="{{$tripOrderSKu1->confirm_qty}}"
                                                               placeholder="Name" required="required" type="text">
                                                    </td>
                                                    <td><input readonly id="name"
                                                               class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               value="{{$tripOrderSKu1->delivery_qty}}"
                                                               placeholder="Name" required="required" type="text"></td>

                                                    @if($trip->lfcl_id==5)
                                                        <td><input id="name"
                                                                   class="form-control col-md-7 col-xs-12"
                                                                   data-validate-length-range="6"
                                                                   max="{{$tripOrderSKu1->confirm_qty-$tripOrderSKu1->delivery_qty}}"
                                                                   min="0"
                                                                   data-validate-words="2" name="logistic_qty[]"
                                                                   value="{{$tripOrderSKu1->confirm_qty-$tripOrderSKu1->delivery_qty}}"
                                                                   placeholder="0" required="required" type="number">
                                                        </td>
                                                    @else
                                                        <td><input readonly id="name"
                                                                   class="form-control col-md-7 col-xs-12"
                                                                   data-validate-length-range="6"
                                                                   data-validate-words="2" name=""
                                                                   value="{{$tripOrderSKu1->logistic_qty}}"
                                                                   placeholder="Name" required="required" type="text">
                                                        </td>
                                                    @endif


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
                                                <th>Issue QTY</th>
                                                <th>Return QTY</th>
                                                <th>Transfer QTY</th>
                                                <th>good QTY</th>
                                                <th>Damage QTY</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($tripGrvSKu as $index => $tripGrvSKu1)
                                                <tr>
                                                    <td>{{$index+1}}</td>
                                                    <td><input readonly id="name"
                                                               class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               value="{{$tripGrvSKu1->sku_id}}"
                                                               placeholder="Name" required="required" type="text"></td>
                                                    <td>{{$tripGrvSKu1->sku_name}}</td>
                                                    <td>{{$tripGrvSKu1->sku_code}}</td>
                                                    <td><input readonly id="name"
                                                               class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               value="{{$tripGrvSKu1->issued_qty}}"
                                                               placeholder="Name" required="required" type="text"></td>
                                                    <td><input readonly id="name"
                                                               class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               value="{{$tripGrvSKu1->confirm_qty}}"
                                                               placeholder="Name" required="required" type="text">
                                                    </td>
                                                    <td><input readonly id="name"
                                                               class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               value="{{$tripGrvSKu1->delivery_qty}}"
                                                               placeholder="Name" required="required" type="text">
                                                    </td>
                                                    @if($trip->lfcl_id==5 &&$trip->ttyp_id==1)
                                                        <td>
                                                            <input

                                                                    name="trip_grv_sku[]"
                                                                    value="{{$tripGrvSKu1->sku_id}}"
                                                                    required="required" type="hidden">
                                                            <input id="name"
                                                                   class="form-control col-md-7 col-xs-12"
                                                                   data-validate-length-range="6"
                                                                   data-validate-words="2"
                                                                   name="trip_grv_sku_gqty[]"
                                                                   value="{{$tripGrvSKu1->delivery_qty}}"
                                                                   placeholder="Good Qty" required="required"
                                                                   type="number" min="0"></td>
                                                        <td><input id="name"
                                                                   class="form-control col-md-7 col-xs-12"
                                                                   data-validate-length-range="6"
                                                                   data-validate-words="2"
                                                                   name="trip_grv_sku_bqty[]"
                                                                   value="0"
                                                                   placeholder="Damage Qty" required="required"
                                                                   type="number" min="0"></td>
                                                    @else
                                                        <td><input readonly id="name"
                                                                   class="form-control col-md-7 col-xs-12"
                                                                   data-validate-length-range="6"
                                                                   data-validate-words="2"
                                                                   value="{{$tripGrvSKu1->g_qty}}"
                                                                   placeholder="Name" required="required" type="text">
                                                        </td>
                                                        <td><input readonly id="name"
                                                                   class="form-control col-md-7 col-xs-12"
                                                                   data-validate-length-range="6"
                                                                   data-validate-words="2"
                                                                   value="{{$tripGrvSKu1->b_qty}}"
                                                                   placeholder="Name" required="required" type="text">
                                                        </td>
                                                    @endif


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
                                        <h1>Trip</h1>
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
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Trip
                                                Id
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       name="name"
                                                       value="{{$trip->id}}"
                                                       placeholder="Name" required="required" type="text">

                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Employee
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       name="name"
                                                       value="{{$trip->employee()->aemp_usnm.'-'.$trip->employee()->aemp_name}}"
                                                       placeholder="Name" required="required" type="text">

                                            </div>
                                        </div>

                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Depot
                                                Name <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       name="name"
                                                       value="{{$trip->depot()->dlrm_name}}"
                                                       placeholder="Name" required="required" type="text">
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                                @if($trip->lfcl_id==5)
                                                    <button type="submit" class="btn btn-success">Next</button>
                                                @endif
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
@endsection