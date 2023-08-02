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
                            <a href="{{ URL::to('/sales-group-data')}}">All Group</a>
                        </li>
                        <li class="active">
                            <strong>SKU</strong>
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
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>SKU</h1>
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
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span
                                                class="required">*</span>
                                    </label>
                                    <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                           data-validate-length-range="6" data-validate-words="2" name="name"
                                           value="{{$salesGroup->name}}"
                                           placeholder="Name" required="required" type="text">
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Code <span
                                                class="required">*</span>
                                    </label>
                                    <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                           data-validate-length-range="6" data-validate-words="2" name="code"
                                           value="{{$salesGroup->code}}"
                                           placeholder="Code" required="required" type="text">
                                </div>

                            </div>

                            <form style="display:inline"
                                  action="{{ URL::to('sales-group-data/sku_add/'.$salesGroup->id)}}"
                                  class="pull-xs-right5 card-link" method="GET">
                                {{csrf_field()}}
                                {{method_field('DELETE')}}

                                <table class="table table-striped projects">
                                    <thead>

                                    </thead>
                                    <tbody>
                                    <td>
                                        Add SKU
                                    </td>
                                    <td>
                                        <select class="form-control" name="sku_id" id="sku_id"
                                                required>
                                            <option value="">Select</option>
                                            @foreach ($skus as $sku)
                                                <option value="{{ $sku->id }}">{{ $sku->name.' ('.$sku->code.')' }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input class="btn btn-success btn-xs" type="submit"
                                               value="Save"
                                               onclick="">
                                    </td>

                                    </tbody>
                                </table>

                                </input>
                            </form>

                            <table class="table table-striped projects">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>SKU Id</th>
                                    <th>SKU Name</th>
                                    <th>SKU Code</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($salesGroupSKUs as $index=>$salesGroupSKU)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$salesGroupSKU->sku_id}}</td>
                                        <td>{{$salesGroupSKU->sku()->name}}</td>
                                        <td>{{$salesGroupSKU->sku()->code}}</td>
                                        <td>
                                            <form style="display:inline"
                                                  action="{{ URL::to('sales-group-data/sku_delete/'.$salesGroupSKU->id)}}"
                                                  class="pull-xs-right5 card-link" method="GET">
                                                {{csrf_field()}}
                                                {{method_field('DELETE')}}
                                                <input class="btn btn-danger btn-xs" type="submit"
                                                       value="Delete">
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
    <script type="text/javascript">
        $("#sku_id").select2({width: 'resolve'});
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Delete?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
@endsection