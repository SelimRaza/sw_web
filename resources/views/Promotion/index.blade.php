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
                            <strong>All Promotion</strong>
                        </li>
                    </ol>
                </div>
                <form action="{{ URL::to('/promotion')}}" method="get">
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

                                <a href="{{ URL::to('/promotion/create')}}" class="btn btn-success btn-sm">Add New Promotion</a>
                                <a href="{{ URL::to('promotion/create/new')}}" class="btn btn-warning btn-sm">Add New Promotion 2</a>

                            @endif
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            {{$promotion->appends(Request::only('search_text'))->links()}}
                            <table id="datatable" class="table table-bordered table-striped projects" data-page-length='50'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>SL</th>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>Promotion Name</th>
                                    <th>Promotion Code</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Promotion Type</th>
                                    <th>Sales Group</th>
                                    <th>Qualifier</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($promotion as $index => $promotion)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$promotion->amim_code}}</td>
                                        <td>{{$promotion->amim_name}}</td>
                                        <td>{{$promotion->prom_name}}</td>
                                        <td>{{$promotion->prom_code}}</td>
                                        <td>{{$promotion->prom_sdat}}</td>
                                        <td>{{$promotion->prom_edat}}</td>
                                        <td>{{ $promotion->prom_nztp == '0' ? 'National' : 'Zonal'}}</td>
                                        <td>{{$promotion->slgp_name}}</td>
                                        <td>{{ $promotion->prom_type == '0' ? 'Quantity' : 'Value'}}</td>
                                        <td>
                                            {{-- @if($permission->wsmu_read)
                                                 <a href="{{ URL::to('depot/stock/'.$depot->id)}}"
                                                    class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> Stock
                                                 </a>
                                             @endif--}}
                                            @if($permission->wsmu_read)
                                                <a href="{{route('promotion.show',$promotion->prom_id)}}"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View
                                                </a>
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <a href="{{route('promotion.edit',$promotion->prom_id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit
                                                </a>
                                                {{--<button type="button" class="btn btn-info btn-xs" data-toggle="modal"
                                                        data-target="#exampleModalCenter">
                                                    Edit
                                                </button>--}}
                                            @endif
                                            {{--@if($permission->wsmu_updt)
                                                <a href="{{ URL::to('depot/employee/'.$promotion->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i>Employee
                                                </a>
                                            @endif--}}

                                            @if($permission->wsmu_delt)
                                                <form style="display:inline"
                                                      action="{{route('promotion.destroy',$promotion->prom_id)}}"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="<?php echo $promotion->lfcl_id == 1 ? 'Active' : 'Inactive'?>"
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

    {{--promotion data extend modal start--}}

    <!-- Button trigger modal -->


    <!-- Modal -->
    {{--<form class="form-horizontal form-label-left"
          action="{{route('promotion.update',$promotion->id)}}" enctype="multipart/form-data"
          method="post">
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #2b4570; color: white;">
                        <center><h5 class="modal-title" id="exampleModalLongTitle"><strong>Extend Promotion
                                    Date</strong>
                            </h5></center>
                    </div>
                    <div class="modal-body">

                        {{csrf_field()}}
                        {{method_field('PUT')}}


                        <div class="item form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Start Date <span
                                        class="required">*</span>
                            </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input id="startDate" name="startDate" class="form-control col-md-7 col-xs-12"
                                       data-validate-length-range="6" data-validate-words="2"
                                       placeholder="Name" required="required" type="text"
                                       value="{{$editPromotion->prom_sdat}}">
                            </div>
                        </div>
                        <div class="item form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">End Date <span
                                        class="required">*</span>
                            </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input id="endDate" name="endDate" class="form-control col-md-7 col-xs-12"
                                       data-validate-length-range="6" data-validate-words="2"
                                       placeholder="Code" required="required" type="text"
                                       value="{{$editPromotion->prom_edat}}">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>--}}
    {{--promotion data extend modal end--}}
    <script type="text/javascript">
        $('#startDate').datetimepicker({format: 'YYYY-MM-DD'});
        $('#endDate').datetimepicker({format: 'YYYY-MM-DD'});
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
@endsection