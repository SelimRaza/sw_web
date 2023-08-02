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
                            <strong>All Trip</strong>
                        </li>
                        @if($permission->wsmu_crat)
                            <li class="label-success">
                                <a href="{{ URL::to('/trip/create')}}">New Trip</a>
                            </li>
                        @endif
                    </ol>
                </div>
                <form action="{{ URL::to('/trip')}}" method="get">
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
                            <h1>Trip</h1>
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
                            {{$trips->appends(Request::only('search_text'))->links()}}
                            <table class="table table-striped projects">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Id</th>
                                    <th>Date</th>
                                    <th>Emp Id</th>
                                    <th>Emp Name</th>
                                    <th>Depot Id</th>
                                    <th>Depot Name</th>
                                    <th>Trip Type</th>
                                    <th>Status</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($trips as $index => $trip)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$trip->id}}</td>
                                        <td>{{$trip->trip_date}}</td>
                                        <td>{{$trip->emp_id}}</td>
                                        <td>{{$trip->employee()->aemp_name.'('.$trip->employee()->aemp_usnm.')'}}</td>
                                        <td>{{$trip->dlrm_id}}</td>
                                        <td>{{$trip->depot()->dlrm_name}}</td>
                                        <td>{{$trip->tripType()->ttyp_name}}</td>
                                        <td>{{$trip->status()->lfcl_name}}</td>
                                        <td>
                                            @if($permission->wsmu_read)
                                                <a href="{{route('trip.show',$trip->id)}}"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> Trip View
                                                </a>
                                            @endif
                                            @if($permission->wsmu_updt)
                                                @if($trip->lfcl_id==1)
                                                    <a href="{{route('trip.edit',$trip->id)}}"
                                                       class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Order
                                                        Assign
                                                    </a>
                                                @endif
                                            @endif
                                            @if($permission->wsmu_updt)
                                                @if($trip->lfcl_id==1)
                                                    <a href="{{ URL::to('trip/grv/'.$trip->id)}}"
                                                       class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> GRV
                                                        Assign
                                                    </a>
                                                @endif
                                            @endif
                                            @if($permission->wsmu_updt)
                                                @if($trip->lfcl_id==1)
                                                    <a href="{{ URL::to('trip/product/'.$trip->id)}}"
                                                       class="btn btn-info btn-xs"><i class="fa fa-pencil"></i>Product
                                                        Verify
                                                    </a>
                                                @endif
                                            @endif

                                            @if($permission->wsmu_delt)
                                                @if($trip->lfcl_id==1)
                                                    <form style="display:inline"
                                                          action="{{route('trip.destroy',$trip->id)}}"
                                                          class="pull-xs-right5 card-link" method="POST">
                                                        {{csrf_field()}}
                                                        {{method_field('DELETE')}}
                                                        <input class="btn btn-danger btn-xs" type="submit"
                                                               value="<?php echo $trip->lfcl_id == 1 ? 'Inactive' : 'Active'?>"
                                                        >
                                                        </input>
                                                    </form>
                                                @endif
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