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
                            <a href="{{ URL::to('/channel')}}">All Channel</a>
                        </li>
                        <li class="active">
                            <strong>New Channel</strong>
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

                            <button class="btn btn-success btn-sm" onclick="getAddChannel();"><span
                                        class="fa fa-plus-circle" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Add
                                    New Channel</b></button>
                            <button class="btn btn-success btn-sm" onclick="addUploadFile();"><span
                                        class="fa fa-cloud-upload" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Upload
                                    File</b></button>

                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content" id="add_channel">

                            <form class="form-horizontal form-label-left" action="{{route('channel.store')}}"
                                  method="post">
                                {{csrf_field()}}


                                <div class="item form-group">
                                    <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Channel Name <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="name"
                                               placeholder="Channel Name" required="required" type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Channel Code <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="code"
                                               placeholder="Channel Code" required="required" type="text">
                                    </div>
                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <button id="send" type="submit" class="btn btn-primary"><i class="fa fa-check-circle"></i> Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>


                        <div class="x_content" id="upload_channel">
                            <form class="form-horizontal form-label-left"
                                  action="{{URL::to('/channel/file/insert')}}"
                                  method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <strong>
                                    <center>::: Upload File :::</center>
                                </strong>
                                <div class="ln_solid"></div>
                                <div class="col-md-12">
                                    <a class="btn btn-danger btn-sm" href="{{ URL::to('channel/format/download')}}"><span
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
        $('#upload_channel').hide();
        function getAddChannel() {
            $('#upload_channel').hide();
            $('#add_channel').show();
        }

        function addUploadFile() {
            $('#upload_channel').show();
            $('#add_channel').hide();
        }
    </script>
@endsection