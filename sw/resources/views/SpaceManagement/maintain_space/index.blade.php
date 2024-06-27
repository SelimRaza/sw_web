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
                            <strong>All Spaces</strong>
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

                                <a href="{{ URL::to('/maintain/space/create')}}" class="btn btn-success btn-sm">Add Space</a>
                                <a href="{{ URL::to('/trade-marketing/zone/')}}" class="btn btn-success btn-sm">Trade Marketing Zone</a>

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
                            {{$spaces->links()}}

                            <table id="datatabless" class="table search-table font_color" data-page-length='50'>
                                <thead>
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border">SL</th>
                                    <th>Space Name</th>
                                    <th>Space Code</th>
                                    <th>Group Name</th>
                                    <th>Group Code</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
{{--                                    <th>Qualifier</th>--}}
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tbody id="cont">
                                @foreach($spaces as $i=>$space)
                                    <tr>
                                        <td>{{$i+1}}</td>
                                        <td>{{$space->spcm_name}}</td>
                                        <td>{{$space->spcm_code}}</td>
                                        <td>{{optional($space->saleGroup)->slgp_name}}</td>
                                        <td>{{optional($space->saleGroup)->slgp_code}}</td>
                                        <td>{{$space->spcm_sdat}}</td>
                                        <td>{{$space->spcm_exdt}}</td>
{{--                                        <td>{{$space->spcm_qyfr==1 ? 'Value' : 'FOC' }}</td>--}}
                                        <td>
                                            <a href="{{URL::to('/maintain/space/'.$space->id.'/edit')}}" class="btn btn-info btn-xs">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>&nbsp;|&nbsp;
                                            <a href="{{URL::to('/maintain/space/'.$space->id)}}" class="btn btn-primary btn-xs">
                                                <i class="fa fa-search"></i> View
                                            </a>
{{--                                            &nbsp;|&nbsp;--}}
{{--                                            <a href="{{URL::to('/updateSpaceSiteMapping/'.$space->id)}}" class="btn btn-success btn-xs">--}}
{{--                                                <i class="fa fa-edit"></i> Sites--}}
{{--                                            </a>--}}
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
