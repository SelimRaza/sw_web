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
                        <li>
                            <strong>All Outlet</strong>
                        </li>
                    </ol>
                </div>
                <form action="{{ URL::to('/site')}}" method="get">
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
                                <a class="btn btn-success btn-sm" href="{{ URL::to('/site/create')}}">Add New</a>
                                <a class="btn btn-success btn-sm" href="{{ URL::to('/site/siteUpload')}}">Upload</a>
                                <a class="btn btn-success btn-sm" href="{{ URL::to('/site/unverified')}}">Unverified List</a>
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
                            {{$sites->appends(Request::only('search_text'))->links()}}
                            <table id="datatable" class="table table-bordered projects" data-page-length='500'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>SL</th>
                                    <th>Outlet Id</th>
                                    <th>Outlet Code</th>
                                    <th>Outlet Name1</th>
                                    <th>Outlet Address1</th>
                                    <th>Outlet Mobile1</th>
                                    <th>Outlet Mobile2</th>
                                    <th>Outlet Name2</th>
                                    <th>Outlet Address2</th>
                                    <th>Outlet Owner</th>
                                    <th>Sub chanel</th>
                                    <th>Category</th>
                                    <th>Market</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($sites as $index => $site)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$site->site_id}}</td>
                                        <td>{{$site->site_code}}</td>
                                        <td>{{$site->site_name}}</td>
                                        <td>{{$site->site_adrs}}</td>
                                        <td>{{$site->site_mob1}}</td>
                                        <td>{{$site->site_mob2}}</td>
                                        <td>{{$site->site_olnm}}</td>
                                        <td>{{$site->site_olad}}</td>
                                        <td>{{$site->site_olon}}</td>
                                        <td>{{$site->scnl_name}}</td>
                                        <td>{{$site->otcg_name}}</td>
                                        <td>{{$site->mktm_name}}</td>
                                        <td>
                                            @if($permission->wsmu_read)
                                                <a href="{{route('site.show',$site->site_id)}}"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View
                                                </a>
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <a href="{{route('site.edit',$site->site_id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit
                                                </a>
                                            @endif
                                            @if($permission->wsmu_delt)
                                                <form style="display:inline"
                                                      action="{{route('site.destroy',$site->site_id)}}"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="<?php echo $site->lfcl_id == 1 ? 'Active' : 'Inactive'?>"
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