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
                            <strong>Trade Marketing</strong>
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
                                <a href="{{ URL::to('/maintain/space')}}" class="btn btn-success btn-sm">Space Management</a>

                                <a href="{{ URL::to('/trade-marketing/zone/create')}}" class="btn btn-success btn-sm">Add Trade Marketing Zone</a>

                            @endif

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <table id="datatabless" class="table search-table font_color" data-page-length='50'>
                                <thead>
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border">SL</th>
                                    <th>Staff ID</th>
                                    <th>Staff Name</th>
                                    <th>Zone Code</th>
                                    <th>Zone Name</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tbody id="cont">
                                @foreach($trades as $i=>$trade)
                                    <tr>
                                        <td>{{$i+1}}</td>
                                        <td>{{optional($trade->employee)->aemp_usnm}}</td>
                                        <td>{{optional($trade->employee)->aemp_name}}</td>
                                        <td>{{optional($trade->zone)->zone_code}}</td>
                                        <td>{{optional($trade->zone)->zone_name}}</td>
                                        <td>
                                            <a href="{{URL::to('/trade-marketing/info/'.$trade->id)}}" class="btn btn-primary btn-xs">
                                                <i class="fa fa-search"></i> View
                                            </a>
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
@endsection
