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
                            <strong>All Dashboard Permission</strong>
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
                            <h4><strong>Dashboard Permission</strong></h4>
                            <div class="clearfix"></div>

                            @if($permission->wsmu_crat)
                                <a class="btn btn-success btn-sm" href="{{ URL::to('/dashboardPermission/create')}}" style="float: right; margin-top: -30px;"><span
                                            class="fa fa-plus-circle" style="color: white; font-size: 1.3em;"></span>&nbsp;&nbsp;<b>Add
                                        New</b></a>
                            @endif
                        </div>
                        <div class="x_content">

                            <table class="table table-striped projects">
                                <thead>
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border">SL</th>
                                    <th>Id</th>
                                    <th>Assign User</th>
                                    <th>Dashboard User</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($dataDashboardPermissions as $index=> $dataDashboardPermission)
                                    <tr class="tbl_body_gray">
                                        <td class="cell_left_border">{{$index+1}}</td>
                                        <td>{{$dataDashboardPermission->id}}</td>
                                        <td>{{$dataDashboardPermission->assign_user}}</td>
                                        <td>{{$dataDashboardPermission->dashboard_user}}</td>
                                        <td>
                                            @if($permission->wsmu_delt)
                                                <form style="display:inline"
                                                      action="{{route('dashboardPermission.destroy',$dataDashboardPermission->id)}}"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="Delete"
                                                           >
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
    </script>
@endsection