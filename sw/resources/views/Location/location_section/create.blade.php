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
                            <a href="{{ URL::to('/location_section')}}">All Location Section</a>
                        </li>
                        <li class="active">
                            <strong>New Location Section</strong>
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

                            <button class="btn btn-success btn-sm" onclick="getAddLocationSection();"><span
                                        class="fa fa-plus-circle" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Add
                                    New Location Section</b></button>
                            <button class="btn btn-success btn-sm" onclick="addUploadFile();"><span
                                        class="fa fa-cloud-upload" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Upload
                                    File</b></button>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" id="create_location_section">

                            <form class="form-horizontal form-label-left" action="{{route('location_section.store')}}"
                                  method="post" enctype="multipart/form-data">
                                {{csrf_field()}}

                                <div class="item form-group">
                                    <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Location
                                        Department
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="ldpt_id" id="ldpt_id"
                                                required>
                                            <option value="">Select</option>
                                            @foreach ($locationDepartment as $locationDepartment1)
                                                <option value="{{ $locationDepartment1->id }}">{{ $locationDepartment1->ldpt_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Name <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input class="form-control col-md-7 col-xs-12" maxlength="100"
                                               data-validate-length-range="6" data-validate-words="2" name="lsct_name"
                                               placeholder="Name" required="required" type="text"
                                               value="{{old('lsct_name')}}">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Code <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input class="form-control col-md-7 col-xs-12" maxlength="10"
                                               data-validate-length-range="6" data-validate-words="2" name="lsct_code"
                                               placeholder="Code" required="required" type="text"
                                               value="{{old('lsct_code')}}">
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


                        <div class="x_content" id="upload_location_section">
                            <form class="form-horizontal form-label-left"
                                  action="{{URL::to('/location_section/upload')}}"
                                  method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <strong>
                                    <center>::: Upload File :::</center>
                                </strong>
                                <div class="ln_solid"></div>
                                <div class="col-md-12">
                                    <a class="btn btn-danger btn-sm"
                                       href="{{ URL::to('location_section/format')}}"><span
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
        $('#upload_location_section').hide();
        function getAddLocationSection() {
            $('#upload_location_section').hide();
            $('#create_location_section').show();
        }

        function addUploadFile() {
            $('#upload_location_section').show();
            $('#create_location_section').hide();
        }

        const ldpt_id = '{{ old('ldpt_id') }}';
        if (ldpt_id !== '') {
            $('#ldpt_id').val(ldpt_id);
        }
        $("#ldpt_id").select2({width: 'resolve'});

    </script>

@endsection