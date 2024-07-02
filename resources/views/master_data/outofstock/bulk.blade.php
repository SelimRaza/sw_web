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
                        <li class="label-success">
                            <a href="{{ URL::to('/outofstock')}}">All OutOfStock</a>
                        </li>
                        <li class="active">
                            <strong>New OutOfStock</strong>
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
                            <h1 >OutOfStock</h1>

                            @if($permission->wsmu_crat)
                                <a href="{{ URL::to('/bulk/outofstock/format')}}" style="text-decoration:underline">Click here to download format</a>
                                <button class="btn btn-danger" onclick="closeWindow()" style=" float:right;">Close</button>
                            @endif
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form id="demo-form2" data-parsley-validate
                                  class="form-horizontal form-label-left"
                                  action="{{ URL::to('/bulk/outofstock')}}"
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
                                               placeholder="Out of Stock file" type="file">
                                        <ul style="padding-left: 1.35rem">
                                            <li class="text-warning">Status 1 for Stock Out</li>
                                            <li class="text-warning">Status 2 for Stock In</li>
                                        </ul>

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
        </div>
    </div>
@endsection