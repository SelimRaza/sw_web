@extends('theme.app')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li class="label-success">
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li class="label-success">
                            <a href="{{ URL::to('/price_list')}}">All Price List </a>
                        </li>
                        <li>
                            <strong>Price List Details</strong>
                        </li>
                        <li class="label-success">
                            <a
                                    href="{{ URL::to('/price_list/skuUploadFormat/'.$priceList->id)}}">Generate
                                SKU
                                Upload
                                Format </a>
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
                        <strong>Success! </strong>{{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert-danger">
                        <strong>Alert! </strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12">
                @if($permission->wsmu_updt)
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
                                    <div class="x_title">

                                        <h2>{{$priceList->name}}
                                            <small>{{$priceList->code}}</small>
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
                                        <form id="demo-form2" data-parsley-validate
                                              class="form-horizontal form-label-left"
                                              action="{{ URL::to('price_list/sku_add/'.$priceList->id)}}"
                                              method="post">
                                            {{csrf_field()}}
                                            {{method_field('POST')}}

                                            <div class="col-md-6">

                                                <div class="item form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">
                                                        DP per CTN <span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <input class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               name="pldt_dppr"
                                                               value="0"
                                                               placeholder="DP per CTN" step="any" required="required"
                                                               type="number">
                                                    </div>
                                                </div>
                                                <div class="item form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">DP GRV per
                                                         CTN<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <input class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               name="pldt_dgpr"
                                                               value="0"
                                                               placeholder="DP GRV per
                                                         CTN" step="any" required="required"
                                                               type="number">
                                                    </div>
                                                </div>
                                                <div class="item form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">TP per
                                                        CTN <span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <input class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               name="pldt_tppr"
                                                               value="0"
                                                               placeholder="TP per
                                                        CTN" step="any" required="required"
                                                               type="number">
                                                    </div>
                                                </div>
                                                <div class="item form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">TP
                                                        GRV
                                                         Per CTN<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <input class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               name="pldt_tpgp"
                                                               value="0"
                                                               placeholder="TP
                                                        GRV
                                                        CTN Price" step="any" required="required"
                                                               type="number">
                                                    </div>
                                                </div>

                                                <div class="item form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">MRP Per CTN<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <input class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               name="pldt_mrpp"
                                                               value="0"
                                                               placeholder="MRP Per CTN" step="any" required="required"
                                                               type="number">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="item form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Short
                                                        Name<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <input class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               name="pldt_snme"
                                                               value=""
                                                               placeholder="Short Name" required="required" type="text">
                                                    </div>
                                                </div>
                                                <div class="item form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">CTN
                                                        Size<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <input class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2"
                                                               name="amim_duft"
                                                               min="1"
                                                               value="1"
                                                               placeholder="CTN Size"
                                                               required="required"
                                                               type="number">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                           for="first-name">Add SKU
                                                        <span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <select class="form-control col-md-7 col-xs-12"
                                                                data-validate-length-range="6"
                                                                data-validate-words="2"
                                                                class="form-control" name="amim_id" id="amim_id"
                                                                required>
                                                            <option value="">Select</option>
                                                            @foreach ($skus as $sku)
                                                                <option value="{{ $sku->id }}">{{ $sku->amim_name.' ('.$sku->amim_code.')' }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                           for="first-name">Categroy
                                                        <span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <select class="form-control col-md-7 col-xs-12"
                                                                data-validate-length-range="6"
                                                                data-validate-words="2"
                                                                class="form-control" name="issc_id" id="issc_id"
                                                                required>
                                                                <option value="">Select</option>
                                                                @foreach($groupWishCategoryies as $groupWishCategory)
                                                                    <option value="{{ $groupWishCategory->id }}">{{ $groupWishCategory->issc_name}}</option>
                                                                @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="ln_solid"></div>
                                                <div class="form-group">
                                                    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                                        <button type="submit" class="btn btn-success">Save</button>
                                                    </div>
                                                </div>
                                            </div>


                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($permission->wsmu_updt)
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2>{{$priceList->plmt_name}}
                                            <small>{{$priceList->plmt_code}}</small>
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
                                        <form id="demo-form2" data-parsley-validate
                                              class="form-horizontal form-label-left"
                                              action="{{ URL::to('price_list/skuUpload')}}"
                                              enctype="multipart/form-data"
                                              method="post">
                                            {{csrf_field()}}
                                            {{method_field('POST')}}
                                            <div class="form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                       for="first-name">File
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="name" class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6"
                                                           data-validate-words="2"
                                                           name="import_file"
                                                           placeholder="Shop List file" type="file"
                                                           step="1">
                                                </div>
                                            </div>
                                            <div class="ln_solid"></div>
                                            <div class="form-group">
                                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                                    <button type="submit" class="btn btn-success">Upload
                                                    </button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">
                                    @if($permission->wsmu_crat)

                                    @endif
                                    <h2>{{$priceList->plmt_name}}
                                        <small>{{$priceList->plmt_code}}</small>
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
                                    <table id="datatable" class="table table-bordered projects" data-page-length='100'>
                                        <thead>
                                        <tr class="tbl_header">
                                            <th>S/L</th>
                                            <th>SKU Id</th>
                                            <th>SKU Name</th>
                                            <th>Display Name</th>
                                            <th>SKU Code</th>
                                            <th>CTN Size</th>
                                            <th>Dealer CTN Price</th>
                                            <th>Dealer GRV CTN Price</th>
                                            <th>Sales CTN Price</th>
                                            <th>Sales GRV CTN Price</th>
                                            <th>MRP CTN Price</th>
                                            <th style="width: 20%">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($priceListDetails  as  $index =>$priceListDetail)
                                            <tr>
                                                <td>{{$index+1 }}</td>
                                                <td>{{$priceListDetail->amim_id}}</td>
                                                <td>{{$priceListDetail->sku()->amim_name}}</td>
                                                <td>{{$priceListDetail->pldt_snme}}</td>
                                                <td>{{$priceListDetail->sku()->amim_code}}</td>
                                                <td>{{$priceListDetail->amim_duft}}</td>
                                                <td>{{$priceListDetail->pldt_dppr*$priceListDetail->amim_duft}}</td>
                                                <td>{{$priceListDetail->pldt_dgpr*$priceListDetail->amim_duft}}</td>
                                                <td>{{$priceListDetail->pldt_tppr*$priceListDetail->amim_duft}}</td>
                                                <td>{{$priceListDetail->pldt_tpgp*$priceListDetail->amim_duft}}</td>
                                                <td>{{$priceListDetail->pldt_mrpp*$priceListDetail->amim_duft}}</td>
                                                @if($permission->wsmu_updt)
                                                    <td>
                                                        <form style="display:inline"
                                                              action="{{ URL::to('price_list/sku_delete/'.$priceListDetail->id)}}"
                                                              class="pull-xs-right5 card-link" method="GET">
                                                            {{csrf_field()}}
                                                            {{method_field('DELETE')}}
                                                            <input class="btn btn-danger btn-xs" type="submit"
                                                                   value="Item Price Delete">
                                                            </input>
                                                        </form>
                                                        @if(Auth::user()->employee()->id==34478 || Auth::user()->country()->id !=2)
                                                        <a  href="{{ URL::to('price_list/sku_item/'.$priceList->id.'/'.$priceListDetail->id)}}"
                                                           class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Item Price Edit
                                                        </a>
                                                        @endif

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
                    

                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">

        $("#amim_id").select2({width: 'resolve'});
        $("#issc_id").select2({width: 'resolve'});
    </script>
@endsection