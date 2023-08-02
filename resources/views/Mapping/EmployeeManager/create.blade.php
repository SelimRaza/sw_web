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
                            <strong>Employee Manager Mapping</strong>
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

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">

                            <div class="x_content">

                                <form class="form-horizontal form-label-left" action="{{url('manager-mapping')}}"
                                      method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                        <div class="x_title">
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Staff ID<span
                                                        class="required">*</span></label>
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                <input id="staff-id" name="staff_id" class="form-control in_tg col-md-7 col-xs-12"
                                                       placeholder="Enter Staff ID" required
                                                       type="text">
                                            </div>

                                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Manager ID
                                                 <span class="required">*</span>
                                            </label>
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                <input id="manager-id" name="manager_id" class="form-control in_tg col-md-7 col-xs-12"
                                                       placeholder="Enter Manager ID" required
                                                       type="text">
                                            </div>
                                        </div>

                                        <div class="text-center" style="display: flex; justify-content: center;">
                                            <button class="btn btn-success col-md-2 col-sm-2" style="width: 5vw;"
                                                    type="submit" > Update
                                            </button>
                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-12">
                        @if($permission->wsmu_crat)
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="x_panel">
                                        <div class="x_title">
                                            <h3>Employee Manager Mapping Bulk Upload</h3>
                                            @if($permission->wsmu_crat)
                                                <a href="{{ URL::to('/manager-mapping-format')}}" style="text-decoration:underline">Click here to download format</a>
{{--                                                <button class="btn btn-danger" onclick="closeWindow()" style=" float:right;">Close</button>--}}
                                            @endif
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="x_content">
                                            <br/>
                                            <form id="demo-form2" data-parsley-validate
                                                  class="form-horizontal form-label-left"
                                                  action="{{ URL::to('/manager-mapping-upload')}}"
                                                  enctype="multipart/form-data"
                                                  method="post">
                                                {{csrf_field()}}
                                                {{method_field('POST')}}
                                                <div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                           for="first-name">File
                                                        <span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6"
                                                               data-validate-words="2"
                                                               name="import_file"
                                                               placeholder="Shop List file" type="file"
                                                               step="1">
                                                    </div>
                                                </div>
                                                <div class="ln_solid"></div>
                                                <div class="form-group">
                                                    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                                        <button type="submit" class="btn btn-success">Upload
                                                        </button>
                                                    </div>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection