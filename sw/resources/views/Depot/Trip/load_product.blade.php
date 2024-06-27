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
                            <a href="{{ URL::to('/trip/'.$loadMaster->trip_id)}}">Trip View</a>
                        </li>
                        <li class="active">
                            <strong>Load Product</strong>
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
                          action="{{ URL::to('/trip/loadVerify/'.$loadMaster->id)}}" enctype="multipart/form-data"
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
                                        <h1>Load Items</h1>

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
                                                <th>Request QTY</th>
                                                <th>Load QTY</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($tripLoadLine as $index => $tripLoadLine1)
                                                <tr>
                                                    <td>{{$index+1}}</td>
                                                    <td><input readonly id="name"
                                                               class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               name="load_sku[]"
                                                               value="{{$tripLoadLine1->sku_id}}"
                                                               placeholder="Name" required="required" type="text"></td>
                                                    <td @if($tripLoadLine1->stock_qty<$tripLoadLine1->request_qty)
                                                        style="color:red"
                                                            @endif>{{$tripLoadLine1->sku_name}}</td>
                                                    <td>{{$tripLoadLine1->sku_code}}</td>
                                                    <td>{{$tripLoadLine1->stock_qty}}</td>
                                                    <td><input readonly id="name"
                                                               class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               name="request_qty[]"
                                                               value="{{$tripLoadLine1->request_qty}}"
                                                               placeholder="Name" required="required" type="text"></td>
                                                    @if($loadMaster->lfcl_id==1)
                                                        <td><input id="name"
                                                                   class="form-control col-md-7 col-xs-12"

                                                                   data-validate-length-range="6"
                                                                   max="{{$tripLoadLine1->stock_qty}}" min="0"
                                                                   data-validate-words="2" name="stock_qty[]"
                                                                   value="{{$tripLoadLine1->request_qty}}"
                                                                   placeholder="0" required="required" type="number">
                                                        </td>

                                                    @else
                                                        <td><input readonly id="name"
                                                                   class="form-control col-md-7 col-xs-12"
                                                                   data-validate-length-range="6"
                                                                   data-validate-words="2" name="load_qty[]"
                                                                   value="{{$tripLoadLine1->load_qty}}"
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
                                        <h2>{{$loadMaster->id}}
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
                                                   for="first-name">Load
                                                <span class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="load_id" name="load_id"
                                                       class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       placeholder="load_id 2" type="text" value="{{$loadMaster->id}}">
                                            </div>
                                        </div>


                                        <div class="ln_solid"></div>
                                        <div class="form-group">
                                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                                @if($loadMaster->lfcl_id==1)
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
    <script type="text/javascript">


    </script>
@endsection