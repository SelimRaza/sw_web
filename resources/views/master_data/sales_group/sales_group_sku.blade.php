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
                            <a href="{{ URL::to('/sales-group')}}">All Group</a>
                        </li>
                        <li class="active">
                            <strong>Sales SKU</strong>
                        </li>
                        @if($permission->wsmu_crat)
                            <li class="label-success">
                                <a href="{{ URL::to('/sales-group/groupSKUMappingUploadFormatGen')}}">Group SKU  Mapping Format</a>
                            </li>
                        @endif
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
                    <div class="col-md-12">
                        @if($permission->wsmu_updt)
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="x_panel">
                                        <div class="x_title">
                                            <h2>{{$salesGroup->slgp_name}}
                                                <small>{{$salesGroup->slgp_code}}</small>
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
                                                  action="{{ URL::to('sales-group/sku_add/'.$salesGroup->id)}}"
                                                  method="GET">
                                                {{csrf_field()}}
                                                {{method_field('PUT')}}
                                                <div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                           for="first-name">SKU List
                                                        <span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                        <select class="form-control" name="sku_id" id="sku_id"
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
                                                           for="first-name">Category List
                                                        <span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                        <select class="form-control" name="category_id" id="category_id"
                                                                required>
                                                            <option value="">Select</option>
                                                            @foreach ($categorys as $category)
                                                                <option value="{{ $category->id }}">{{ $category->issc_name.' ('.$category->issc_code.')' }}</option>
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
                                            <h2>{{$salesGroup->slgp_name}}
                                                <small>{{$salesGroup->slgp_code}}</small>
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
                                                  action="{{ URL::to('/sales-group/groupSkuMappingUpload')}}"
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
                                        <h2>{{$salesGroup->name}}
                                            <small>{{$salesGroup->code}}</small>
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
                                        <table class="table table-striped projects">
                                            <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>SKU Id</th>
                                                <th>SKU Name</th>
                                                <th>SKU Code</th>
                                                <th>Category</th>
                                                <th style="width: 20%">Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($salesGroupSKUs as $index=>$salesGroupSKU)
                                                <tr>
                                                    <td>{{$index+1}}</td><td>{{$salesGroupSKU->sku_id}}</td>
                                                    <td>{{$salesGroupSKU->name}}</td>
                                                    <td>{{$salesGroupSKU->code}}</td>
                                                    <td>{{$salesGroupSKU->issc_name}}</td>
                                                    <td>
                                                        <form style="display:inline"
                                                              action="{{ URL::to('sales-group/sku_active_inactive/'.$salesGroupSKU->id)}}"
                                                              class="pull-xs-right5 card-link" method="GET">
                                                            {{csrf_field()}}
                                                            {{method_field('DELETE')}}
                                                            <input class="btn btn-info btn-xs" type="submit"
                                                                   value="<?php echo $salesGroupSKU->lfcl_id == 1 ? 'Active' : 'Inactive'?>"
                                                            >
                                                            </input>
                                                        </form>
                                                        <form style="display:inline"
                                                              action="{{ URL::to('sales-group/sku_delete/'.$salesGroupSKU->id)}}"
                                                              class="pull-xs-right5 card-link" method="GET">
                                                            {{csrf_field()}}
                                                            {{method_field('DELETE')}}
                                                            <input class="btn btn-danger btn-xs" type="submit"
                                                                   value="Delete"
                                                            >
                                                            </input>
                                                        </form>

                                                    </td>
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
        $("#sku_id").select2({width: 'resolve'});
        $("#category_id").select2({width: 'resolve'});
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Delete?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
@endsection