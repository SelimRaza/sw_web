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
                            <a href="{{ URL::to('/route')}}">All Route</a>
                        </li>
                        <li class="active">
                            <strong>Show Route</strong>
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
                <div class="col-md-12 col-sm-12 col-xs-12">


                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">
                                    <h1>Route </h1>
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

                                    <form class="form-horizontal form-label-left"
                                          action="{{route('route.show',$route->id)}}"
                                          method="post">
                                        {{csrf_field()}}
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Base
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       name="name"
                                                       value="{{$route->base()->base_name}}"
                                                       placeholder="Name" required="required" type="text">

                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Company
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       name="name"
                                                       value="{{$route->company()->acmp_name}}"
                                                       placeholder="Name" required="required" type="text">

                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       name="name"
                                                       value="{{$route->rout_name}}"
                                                       placeholder="Name" required="required" type="text">
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Code
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       name="code"
                                                       value="{{$route->rout_code}}"
                                                       placeholder="Code" required="required" type="text">
                                            </div>
                                        </div>


                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2>{{$route->rout_name}}
                                        <small>{{$route->rout_code}}</small>
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
                                    <table id="data_table" class="table table-striped table-bordered"
                                           data-page-length='25'>
                                        <thead>
                                        <tr style="background-color: #2b4570; color: white;">
                                            <th>S/L</th>
                                            <th>User Name</th>
                                            <th>User Code</th>
                                            <th>Day</th>
                                            <th>Group</th>
                                            <th>Zone</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody id="cont">
                                        @foreach ($pjps as $index=> $pjp)
                                            <tr>
                                                <td>{{$index+1}}</td>
                                                <td>{{$pjp->aemp_name}}</td>
                                                <td>{{$pjp->aemp_usnm}}</td>
                                                <td>{{$pjp->rpln_day}}</td>
                                                <td>{{$pjp->slgp_name}}</td>
                                                <td>{{$pjp->zone_name}}</td>
                                                <td>
                                                    @if($permission->wsmu_read)
                                                        <a href="{{ URL::to('route/route_site/'.$route->id)}}"
                                                           class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Site
                                                            List
                                                        </a>
                                                    @endif
                                                    @if($permission->wsmu_updt)
                                                        <form style="display:inline"
                                                              action="{{route('pjp.destroy',$pjp->rpln_id)}}"
                                                              class="pull-xs-right5 card-link" method="POST">
                                                            {{csrf_field()}}
                                                            {{method_field('DELETE')}}
                                                            <input class="btn btn-danger btn-xs" type="submit"
                                                                   value="Delete"
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
        </div>
    </div>
@endsection