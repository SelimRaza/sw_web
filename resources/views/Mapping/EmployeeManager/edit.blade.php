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
                            <a href="{{ URL::to('/gps-apps')}}">FAKE GPS</a>
                        </li>
                        <li class="active">
                            <strong>New GPS Apps Information</strong>
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

                                <form class="form-horizontal form-label-left" action="{{route('gps-apps.update',$app->id)}}"
                                      method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    @method('PUT')
                                    <div class="x_title">
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">App Name
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" name="name" class="form-control in_tg col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   placeholder="Enter App Name" value="{{ $app->name ?? '' }}"
                                                   type="text">
                                        </div>

                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">URL
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="Url" name="url" class="form-control in_tg col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   placeholder="Enter App URL" value="{{ $app->url ?? '' }}"
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
                </div>
            </div>
        </div>
    </div>
@endsection