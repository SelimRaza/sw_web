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
                            <a href="{{ URL::to('/cancel_order_reason')}}">All Order Cancel Reason</a>
                        </li>
                        <li class="active">
                            <strong>New Order Cancel Reason</strong>
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

                            <button class="btn btn-success btn-sm" onclick="getAddOCR();"><span
                                        class="fa fa-plus-circle" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Add
                                    New Region</b></button>
                            <button class="btn btn-success btn-sm" onclick="addUploadFile();"><span
                                        class="fa fa-cloud-upload" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Upload
                                    File</b></button>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" id="add_ocrs">

                            <form class="form-horizontal form-label-left"
                                  action="{{route('cancel_order_reason.store')}}"
                                  method="post">
                                {{csrf_field()}}
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Name <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input id="ocrs_name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="ocrs_name" value="{{old('ocrs_name')}}"
                                                   placeholder="Name" required="required" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Code <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input id="ocrs_code" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="ocrs_code" value="{{old('ocrs_code')}}"
                                                   placeholder="Code" required="required" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="ln_solid"></div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <button id="send" type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>


                        <div class="x_content" id="upload_ocrs">
                            <form class="form-horizontal form-label-left"
                                  action="{{URL::to('/ocr/file/insert')}}"
                                  method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <strong>
                                    <center>::: Upload File :::</center>
                                </strong>
                                <div class="ln_solid"></div>
                                <div class="col-md-12">
                                    <a class="btn btn-danger btn-sm" href="{{ URL::to('ocr/format/download')}}"><span
                                                class="fa fa-cloud-download"
                                                style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Download
                                            Format</b></a>
                                </div>
                                <div class="col-md-12">
                                    <input id="name" class="form-control col-md-7 col-xs-12"
                                           data-validate-length-range="6" data-validate-words="2" name="import_file"
                                           placeholder="Shop List file" type="file"
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
                                </div>
                            </form>
                            <br>
                            <br>
                            <br>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $('#upload_ocrs').hide();
        function getAddOCR() {
            $('#upload_ocrs').hide();
            $('#add_ocrs').show();
        }

        function addUploadFile() {
            $('#upload_ocrs').show();
            $('#add_ocrs').hide();
        }
    </script>
@endsection