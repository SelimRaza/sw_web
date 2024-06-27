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
                            <a href="{{ URL::to('/vehicle')}}">All Vehicle</a>
                        </li>
                        <li class="active">
                            <strong>New Vehicle</strong>
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

                            <button class="btn btn-success btn-sm" onclick="getAddVehicle();"><span
                                        class="fa fa-plus-circle" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Add
                                    New Vehicle</b></button>
                            <button class="btn btn-success btn-sm" onclick="addUploadFile();"><span
                                        class="fa fa-cloud-upload" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Upload
                                    File</b></button>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" id="add_vehicle">

                            <form class="form-horizontal form-label-left" action="{{route('vehicle.store')}}"
                                  method="post">
                                {{csrf_field()}}


                                <div class="col-md-6">
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Name <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input id="vhcl_name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="vhcl_name" value="{{old('vhcl_name')}}"
                                                   placeholder="Name" required="required" type="text" maxlength="30">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Vehicle Code
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input id="vhcl_code" class="form-control col-md-7 col-xs-12"
                                                   maxlength="10" name="vhcl_code" value="{{old('vhcl_code')}}"
                                                   placeholder="Vehicle Code" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Vehicle
                                            Category
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <select class="form-control" name="vhcl_type" id="vhcl_type"
                                                    required>
                                                <option value="haich">HAICH</option>
                                                <option value="canter">CANTER</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Depot Name
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <select class="form-control" name="dpot_id" id="dpot_id"
                                                    required>
                                                <option value="">Select Class</option>
                                                @foreach ($depots as $depot)
                                                    <option value="{{ $depot->id }}">{{ $depot->dpot_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Registration
                                            Date

                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input id="vhcl_rdat" class="form-control col-md-7 col-xs-12"
                                                   maxlength="10" name="vhcl_rdat" value="{{old('vhcl_rdat')}}"
                                                   placeholder="Registration Date" type="text">
                                        </div>
                                    </div>


                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Owner
                                            Name

                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input id="vhcl_ownr" class="form-control col-md-7 col-xs-12"
                                                   maxlength="10" name="vhcl_ownr" value="{{old('vhcl_ownr')}}"
                                                   placeholder="Owner Name" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Engine No

                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input id="vhcl_engn" class="form-control col-md-7 col-xs-12"
                                                   maxlength="10" name="vhcl_engn" value="{{old('vhcl_engn')}}"
                                                   placeholder="Engine No" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Chassis
                                            Number
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input id="vhcl_csis" class="form-control col-md-7 col-xs-12"
                                                   maxlength="10" name="vhcl_csis" value="{{old('vhcl_csis')}}"
                                                   placeholder="Chassis Number" type="text">
                                        </div>
                                    </div>


                                </div>

                                <div class="col-md-6">
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Licence
                                            Number</label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input id="vhcl_licn" class="form-control col-md-7 col-xs-12"
                                                   maxlength="10" name="vhcl_licn" value="{{old('vhcl_licn')}}"
                                                   placeholder="Licence Number" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Cubic Capacity
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input id="vhcl_cpct" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="vhcl_cpct" value="{{old('vhcl_cpct')}}"
                                                   placeholder="Cubic
                                            Capacity" min="0" type="text"
                                                   step="any">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Fuel Type
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input id="vhcl_fuel" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="vhcl_fuel" value="{{old('vhcl_fuel')}}"
                                                   placeholder="Fuel Type" min="0" type="text"
                                                   step="1">
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Last Meter
                                            Reading
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input id="vhcl_lmrd" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="vhcl_lmrd" value="{{old('vhcl_lmrd')}}"
                                                   placeholder="Last Meter Reading" min="0" type="text"
                                                   step="any">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Capacity
                                            Weight
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input id="vhcl_cpwt" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="vhcl_cpwt" value="{{old('vhcl_cpwt')}}"
                                                   placeholder="Capacity Weight" min="0" type="text"
                                                   step="any">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Capacity
                                            Height
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input id="vhcl_cpht" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="vhcl_cpht" value="{{old('vhcl_cpht')}}"
                                                   placeholder="Capacity Height" min="0" type="text"
                                                   step="any">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Capacity
                                            Width
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input id="vhcl_cpwd" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="vhcl_cpwd" value="{{old('vhcl_cpwd')}}"
                                                   placeholder="Capacity Width" min="0" type="text"
                                                   step="any">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Capacity
                                            Length
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input id="vhcl_cplg" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="vhcl_cplg" value="{{old('vhcl_cplg')}}"
                                                   placeholder="Capacity Length" min="0" type="text"
                                                   step="any">
                                        </div>
                                    </div>


                                </div>
                                <div class="col-md-12 ">
                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-12 ">
                                            <button id="send" type="submit" class="btn btn-primary"><span
                                                        class="fa fa-check-circle"></span> Submit
                                            </button>
                                        </div>
                                    </div>
                                </div>


                            </form>
                        </div>


                        <div class="x_content" id="upload_vehicle">
                            <form class="form-horizontal form-label-left"
                                  action="{{URL::to('/vehicle/file/insert')}}"
                                  method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <strong>
                                    <center>::: Upload File :::</center>
                                </strong>
                                <div class="ln_solid"></div>
                                <div class="col-md-12">
                                    <a class="btn btn-danger btn-sm" href="{{ URL::to('vehicle/format/download')}}"><span
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
    <script>
        $(document).ready(function () {

            const vhcl_type = '{{ old('vhcl_type') }}';
            const dpot_id = '{{ old('dpot_id') }}';
            if (vhcl_type !== '') {
                $('#vhcl_type').val(vhcl_type);
            }
            if (dpot_id !== '') {
                $('#dpot_id').val(dpot_id);
            }
            $("#vhcl_type").select2({width: 'resolve'});
            $("#dpot_id").select2({width: 'resolve'});
            $('#vhcl_rdat').datetimepicker({format: 'YYYY-MM-DD'});
        });

        $('#upload_vehicle').hide();
        function getAddVehicle() {
            $('#upload_vehicle').hide();
            $('#add_vehicle').show();
        }

        function addUploadFile() {
            $('#upload_vehicle').show();
            $('#add_vehicle').hide();
        }
    </script>
@endsection