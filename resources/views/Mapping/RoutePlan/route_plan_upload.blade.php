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
                            <a href="{{ URL::to('/rpln-mapping')}}">All Route Plan</a>
                        </li>
                        <li class="label-success">
                            <a active>Route Plan Upload</a>
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
                        <strong>Error! </strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12 col-sm-12 col-xs-12">

                    <div class="x_content" id="upload-asset-site-mapping">
                        <form class="form-horizontal form-label-left"
                              action="{{URL::to('/rpln-mapping')}}"
                              method="post" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <strong><center>::: Upload File :::</center></strong>
                            <div class="ln_solid"></div>
                            <div class="col-md-12">
                                <a class="btn btn-danger btn-sm" href="{{ URL::to('rpln-mapping-format')}}"><span
                                        class="fa fa-cloud-download"
                                        style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Download
                                        Format</b></a>
                            </div>
                            <div class="col-md-12">
                                <input id="name" class="form-control col-md-7 col-xs-12"
                                       data-validate-length-range="6" data-validate-words="2" name="import_file"
                                       placeholder="Rout Plan Mapping File" type="file"
                                       step="1">
                            </div>
                            <br/><br/><br/>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <button id="send" type="submit" class="btn btn-primary btn-sm"
                                            style="margin-top: 10px;"><span
                                            class="fa fa-cloud-upload"
                                            style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Upload
                                            File</b></button>
                                </div>
                                <div class="text-danger" style="margin-top: 6.65rem !important; margin-left: -2.85rem;">
                                    <ol style="list-style-type: none">
                                        <li>Status 1 For Insert</li>
                                        <li>Status 2 For Delete</li>
                                        <li>Day 1 For Saturday</li>
                                        <li>Day 2 For Sunday</li>
                                        <li>Day 3 For Monday</li>
                                        <li>Day 4 For Tuesday</li>
                                        <li>Day 5 For Wednesday</li>
                                        <li>Day 6 For Thursday</li>
                                        <li>Day 7 For Friday</li>
                                    </ol>
                                </div>
                            </div>
                        </form>
                        <br>
                        <br>
                        <br>
                        <br>
                    </div>                </div>
            </div>
        </div>
    </div>
@endsection
