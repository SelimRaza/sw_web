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
                            <a href="{{ URL::to('/appMenuGroup')}}">All App Menu Profile</a>
                        </li>
                        <li class="active">
                            <strong>Show App Menu Profile</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
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

                        <div class="x_title">
                            <h4><strong>App Menu Profile</strong></h4>
                            <div class="clearfix"></div>

                        </div>
                        <div class="x_content">

                            <table class="table table-striped projects">
                                <thead>
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border">SL</th>
                                    <th>Menu</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($appMenuGroupLine as $index => $appMenuGroupLine1)
                                    <tr class="tbl_body_gray">
                                        <td class="cell_left_border">{{$index+1}}</td>
                                        <td>{{$appMenuGroupLine1->amnu_name}}</td>
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
@endsection