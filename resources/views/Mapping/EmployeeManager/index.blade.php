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
                            <strong>Fake GPS Apps</strong>
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
                                <a class="btn btn-success btn-sm" href="{{ URL::to('/gps-apps/create')}}"><i class="fa fa-plus-circle"></i> Add New</a>
                            @endif
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table id="datatable" class="table font_color" data-page-length='50'>
                                <thead>
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border">SL</th>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>URL</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($appes as $index => $app)
                                    <tr class="tbl_body_gray">
                                        <td class="cell_left_border">{{ $index+1 }}</td>
                                        <td>{{$app->id}}</td>
                                        <td>{{$app->name}}</td>
                                        <td>{{$app->url}}</td>
                                        <td>
                                            @if($permission->wsmu_updt)
                                                <a href="{{route('gps-apps.edit',$app->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit
                                                </a>
                                            @endif

                                            @if($permission->wsmu_delt)
                                                <form style="display:inline"
                                                      action="{{route('gps-apps.destroy',$app->id)}}"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                        {{csrf_field()}}
                                                        {{method_field('DELETE')}}
                                                    <input class="btn btn-{{$app->lfcl_id == 1 ? 'success' : 'danger'}} btn-xs" type="submit"
                                                           value="{{$app->lfcl_id == 1 ? 'Active' : 'Inactive'}}"
                                                           onclick="return ConfirmDelete()"/>
                                                </form>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <!-- end project list -->

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