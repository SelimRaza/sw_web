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
                        <li class="active">
                            <strong>All Group</strong>
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
                        <strong></strong>{{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert-danger">
                        <strong></strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            @if($permission->wsmu_crat)
                                <a class="btn btn-success btn-sm" href="{{ URL::to('/sales-group/create')}}">Add New</a>
                            @endif
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

                            <table id="datatable" class="table table-bordered projects" data-page-length='50'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>SL</th>
                                    <th>Id</th>
                                    <th>Group Name</th>
                                    <th>Group Code</th>
                                    <th>Company</th>
                                    <th colspan="3">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($salesGroups as $index=>$salesGroup)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$salesGroup->id}}</td>
                                        <td>{{$salesGroup->slgp_name}}</td>
                                        <td>{{$salesGroup->slgp_code}}</td>
                                        <td>{{$salesGroup->company()->acmp_name}}</td>
                                        <td>
                                            @if($permission->wsmu_read)
                                                <a href="{{route('sales-group.show',$salesGroup->id)}}"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View
                                                </a>
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <a href="{{route('sales-group.edit',$salesGroup->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit
                                                </a>
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <a href="{{ URL::to('sales-group/category/'.$salesGroup->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i>Category
                                                </a>
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <a href="{{ URL::to('sales-group/sku/'.$salesGroup->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i>SKU
                                                </a>
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <a href="{{ URL::to('sales-group/employee/'.$salesGroup->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i>Employee
                                                </a>
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <a target="_blank" href="{{ URL::to('price_list/sku/'.$salesGroup->plmt_id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i>Price List
                                                </a>
                                            @endif
                                            @if($permission->wsmu_delt)
                                                <form style="display:inline"
                                                      action="{{route('sales-group.destroy',$salesGroup->id)}}"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="<?php echo $salesGroup->status_id == 1 ? 'Active' : 'Inactive'?>"
                                                           onclick="return ConfirmDelete()">
                                                    </input>
                                                </form>
                                            @endif

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
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
@endsection