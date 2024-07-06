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
                            <strong>All Route Plan</strong>
                        </li>
                    </ol>
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
                                <a class="btn btn-success btn-sm" href="{{ URL::to('rpln-mapping/create')}}">Upload</a>
                            @endif
                            @if($permission->wsmu_updt)
                                <a class="btn btn-success btn-sm" href="{{ URL::to('rpln-mapping/replace')}}">Rout Transfer</a>
                            @endif
                            @if($permission->wsmu_updt)
                                <a class="btn btn-success btn-sm" href="{{ URL::to('rpln-mapping/exchange')}}">Rout Exchange</a>
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
                            <table id="datatable" class="table table-bordered projects" data-page-length='100'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>SL</th>
                                    <th>User Name</th>
                                    <th>Name</th>
                                    <th>Group</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($pjps as $index=>$pjp)
                                    <tr>

                                        <td>{{$index+1}}</td>
                                        <td>{{$pjp->aemp_usnm}}</td>
                                        <td>{{$pjp->name}}</td>
                                        <td>{{$pjp->group_name }}</td>
                                        <td>
                                            @if($permission->wsmu_read )
                                                <a href="{{route('pjp.show',$pjp->emp_id)}}"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> Details
                                                </a>
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
