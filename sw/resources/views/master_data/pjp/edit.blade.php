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
                            <a href="{{ URL::to('/pjp')}}">All Route Plan</a>
                        </li>
                        <li class="active">
                            <strong>Edit Route Plan</strong>
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
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Route Plan
                            </h1>
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
                                  action="{{route('pjp.update',$emp->id)}}" enctype="multipart/form-data"
                                  method="post">
                                {{csrf_field()}}
                                {{method_field('PUT')}}

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="saturday" name="saturday"
                                                   value="Employee" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="saturday" name="saturday"
                                                   value="{{$emp->name}}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <table  class="table table-striped table-bordered">
                                            <thead>
                                            <tr style="background-color: #2b4570; color: white;">
                                                <th> Day</th>
                                                <th>Route</th>
                                            </tr>
                                            </thead>
                                            <tbody id="cont">
                                            @foreach ($pjps as $pjp)
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control" id="saturday"
                                                               name="day[]"
                                                               value="{{$pjp->day}}" readonly>
                                                    </td>
                                                    <td>
                                                        <select class="form-control select2" name="route_id[]">
                                                            <option value="{{$pjp->route_id}}">{{$pjp->route()->name}}</option>
                                                            @foreach ($routes as $route)
                                                                <option value="{{ $route->id }}">{{ $route->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            @endforeach


                                            </tbody>
                                        </table>


                                    </div>
                                </div>

                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button id="send" type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

        $(".select2").select2({ width: 'resolve' });
    </script>
@endsection