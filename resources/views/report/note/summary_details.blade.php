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
                            <strong>All Note Details</strong>
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
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">

                                <div class="x_title">

                                    <h1>{{$emp->aemp_name.'('.$emp->aemp_usnm.')'}}</h1>
                                    <h2>
                                        <small>Note Details</small>
                                    </h2>

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
                                    <div class="col-md-1 col-sm-1 col-xs-12">

                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">

                                    <table class="table table-striped projects">
                                        <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Title</th>
                                            <th>Body</th>
                                            <th>Time</th>
                                            <th>Location</th>
                                            <th>Image</th>

                                        </tr>
                                        </thead>
                                        <tbody id="cont">
                                        @foreach($notes as $note)
                                            <tr>
                                                <td>{{$note->date}}</td>
                                                <td>{{$note->note_type}}</td>
                                                <td>{{$note->title}}</td>
                                                <td>{{$note->note}}</td>
                                                <td>{{$note->time}}</td>
                                                <td>{{$note->geo_addr}}</td>
                                                <td>
                                                    <ul class="list-inline">
                                                        @foreach(explode(",",$note->image_name) as $image)
                                                            <li>
                                                                @if($image!='')
                                                                    <img src="https://images.sihirbox.com/{{$image}}"
                                                                         class="avatar" alt="Avatar">
                                                                @endif
                                                                {{--   <a target="_blank"
                                                                      href="{{ URL::to('/')}}/uploads/{{$image}}"
                                                                      class="btn btn-info btn-xs"><img
                                                                               src="{{ URL::to('/')}}/uploads/{{$image}}"
                                                                               class="avatar">
                                                                   </a>--}}

                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                                <td></td>

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