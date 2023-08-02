@extends('theme.app')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li class="label-success">
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li >
                            <strong>All Market</strong>
                        </li>
                    </ol>
                </div>
                <form action="{{ URL::to('/market')}}" method="get">
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
                            @if($permission->wsmu_crat)
                                <a class="btn btn-success btn-sm" href="{{ URL::to('/market/create')}}"><span
                                            class="fa fa-plus-circle" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Add
                                        New</b></a>

                                        <a class="btn btn-danger btn-sm" style="float: right" href="{{ URL::to('getAllMarket')}}"><span
                                            class="fa fa-cloud-download" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Download
                                        File</b></a>
                                {{-- <a class="btn btn-success btn-sm" href="{{ URL::to('/site/siteUpload')}}">Upload</a>--}}
                            @endif

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            {{$markets->appends(Request::only('search_text'))->links()}}
                            <table id="datatabled" class="table search-table font_color" data-page-length='500'>
                                <thead>
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border">SL</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Ward</th>
                                    <th>Status</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($markets as $index => $market)
                                    <tr class="tbl_body_gray">
                                        <td  class="cell_left_border">{{$index+1}}</td>
                                        <td>{{$market->mktm_name}}</td>
                                        <td>{{$market->mktm_code}}</td>
                                        <td>{{$market->ward_name}}</td>
                                        <td>
                                            @if($permission->wsmu_delt)
                                                <form style="display:inline"
                                                      action="{{route('market.destroy',$market->mktm_id)}}"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <input class="btn btn-round btn-xs"
                                                           style="color:white; background-color: <?php echo $market->lfcl_id == 1 ? '#06993a' : '#9f0e35'?>"
                                                           type="submit"
                                                           value="<?php echo $market->lfcl_id == 1 ? 'Active' : 'Inactive'?>"
                                                           onclick="return ConfirmDelete()">
                                                    </input>
                                                </form>
                                            @else
                                                <span class="badge"
                                                      style="background-color: <?php echo $market->lfcl_id == 1 ? '#06993a' : '#9f0e35'?>"><?php echo $market->lfcl_id == 1 ? 'Active' : 'Inactive'?></span>
                                            @endif


                                        </td>
                                        <td>
                                            @if($permission->wsmu_read)
                                                <a href="{{route('market.show',$market->mktm_id)}}"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View
                                                </a>
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <a href="{{route('market.edit',$market->mktm_id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit
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

        $(document).ready(function(){
            $('table.search-table').tableSearch({
                searchPlaceHolder:'Search Text'
            });
        });
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
@endsection