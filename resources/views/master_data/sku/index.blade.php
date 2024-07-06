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
                            <strong>All SKU</strong>
                        </li>

                    </ol>
                </div>

                <form action="{{ URL::to('/sku')}}" method="get">
                    <div class="title_right">
                        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                            <div class="input-group">

                                <input type="text" class="form-control" name="search_text" placeholder="Search for..."
                                       value="{{$search_text}}">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="submit">Go!</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <div class="x_panel">

                    </div>
                </div>
            </div>

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
                                <a href="{{ URL::to('/sku/create')}}" class="btn btn-success btn-sm">Add New</a>
                                <a href="{{ URL::to('bulk_sku')}}" class="btn btn-success btn-sm">Upload</a>
                            @endif

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            {{$skus->appends(Request::only('search_text'))->links()}}
                            <table id="datatable" class="table table-bordered projects" data-page-length='500'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>SL</th>
                                    <th>code</th>
                                    <th>Name</th>
                                    <th>Short Name</th>
                                    <th>Unit</th>
                                    <th>Subcategory</th>
                                    <th>Class</th>
                                    <th>Image</th>
                                    <th>Image Icon</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($skus as $index=>$sku)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$sku->amim_code}}</td>
                                        <td>{{$sku->amim_name}}</td>
                                        <td>{{$sku->amin_snme}}</td>
                                        <td>{{$sku->amim_duft}}</td>
                                        <td>{{$sku->subCategory()->itsg_name}}</td>
                                        <td>{{$sku->itemClasss()->itcl_name}}</td>
                                        <td>
                                            <ul class="list-inline">
                                                <li>
                                                    @if($sku->amim_imgl)
                                                        <img src="https://sw-bucket.sgp1.cdn.digitaloceanspaces.com/{{$sku->amim_imgl}}"
                                                             class="avatar" alt="Avatar">
                                                    @endif
                                                </li>

                                            </ul>
                                        </td>
                                        <td>
                                            <ul class="list-inline">
                                                <li>
                                                    @if($sku->amim_imic)
                                                        <img src="https://sw-bucket.sgp1.cdn.digitaloceanspaces.com/{{$sku->amim_imic}}"
                                                             class="avatar" alt="Avatar">
                                                    @endif
                                                </li>

                                            </ul>
                                        </td>
                                        <td>
                                            @if($permission->wsmu_read)
                                                <a href="{{route('sku.show',$sku->id)}}"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View
                                                </a>
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <a href="{{route('sku.edit',$sku->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit
                                                </a>
                                            @endif
                                            @if($permission->wsmu_delt)
                                                <form style="display:inline"
                                                      action="{{route('sku.destroy',$sku->id)}}"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <input class="btn btn-{{ $sku->lfcl_id == 1 ? 'success' : 'danger' }} btn-xs" type="submit"
                                                           value="{{ $sku->lfcl_id == 1 ? 'Active' : 'Inactive' }}"
                                                           onclick="return ConfirmDelete('{{$sku->lfcl_id}}')">
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
        function ConfirmDelete(status) {
            let targetStatus = (status === '1') ? 'Inactive' : 'Active';
            var x = confirm(`Are you sure you want to ${targetStatus}?`);
            if (x)
                return true;
            else
                return false;
        };
    </script>
@endsection
