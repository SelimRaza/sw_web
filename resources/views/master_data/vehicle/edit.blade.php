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
                            <strong>Edit Vehicle</strong>
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
                            <h4><strong>Edit Vehicle</strong></h4>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left"
                                  action="{{route('vehicle.update',$vehicle->id)}}"
                                  method="post">
                                {{csrf_field()}}
                                {{method_field('PUT')}}

                                <div class="col-md-6">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="vhcl_name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="vhcl_name" value="{{$vehicle->vhcl_name}}"
                                                   placeholder="Name" required="required" type="text" maxlength="30">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Vehicle Code
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="vhcl_code" class="form-control col-md-7 col-xs-12"
                                                   maxlength="10" name="vhcl_code" value="{{$vehicle->vhcl_code}}"
                                                   placeholder="Vehicle Code" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Vehicle
                                            Category
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="vhcl_type" id="vhcl_type"
                                                    required>
                                                <option value="haich">HAICH</option>
                                                <option value="canter">CANTER</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Depot Name
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
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
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Registration
                                            Date

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="vhcl_rdat" class="form-control col-md-7 col-xs-12"
                                                   maxlength="10" name="vhcl_rdat" value="{{$vehicle->vhcl_rdat}}"
                                                   placeholder="Registration Date" type="text">
                                        </div>
                                    </div>


                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Owner
                                            Name

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="vhcl_ownr" class="form-control col-md-7 col-xs-12"
                                                   maxlength="10" name="vhcl_ownr" value="{{$vehicle->vhcl_ownr}}"
                                                   placeholder="Owner Name" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Engine No

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="vhcl_engn" class="form-control col-md-7 col-xs-12"
                                                   maxlength="10" name="vhcl_engn" value="{{$vehicle->vhcl_engn}}"
                                                   placeholder="Engine No" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Chassis
                                            Number
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="vhcl_csis" class="form-control col-md-7 col-xs-12"
                                                   maxlength="10" name="vhcl_csis" value="{{$vehicle->vhcl_csis}}"
                                                   placeholder="Chassis Number" type="text">
                                        </div>
                                    </div>


                                </div>

                                <div class="col-md-6">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Licence
                                            Number</label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="vhcl_licn" class="form-control col-md-7 col-xs-12"
                                                   maxlength="10" name="vhcl_licn" value="{{$vehicle->vhcl_licn}}"
                                                   placeholder="Licence Number" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Cubic
                                            Capacity
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="vhcl_cpct" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="vhcl_cpct" value="{{$vehicle->vhcl_cpct}}"
                                                   placeholder="Cubic
                                            Capacity" min="0" type="text"
                                                   step="any">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Fuel Type
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="vhcl_fuel" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="vhcl_fuel" value="{{$vehicle->vhcl_fuel}}"
                                                   placeholder="Fuel Type" min="0" type="text"
                                                   step="1">
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Last Meter
                                            Reading
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="vhcl_lmrd" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="vhcl_lmrd" value="{{$vehicle->vhcl_lmrd}}"
                                                   placeholder="Last Meter Reading" min="0" type="text"
                                                   step="any">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Capacity
                                            Weight
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="vhcl_cpwt" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="vhcl_cpwt" value="{{$vehicle->vhcl_cpwt}}"
                                                   placeholder="Capacity Weight" min="0" type="text"
                                                   step="any">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Capacity
                                            Height
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="vhcl_cpht" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="vhcl_cpht" value="{{$vehicle->vhcl_cpht}}"
                                                   placeholder="Capacity Height" min="0" type="text"
                                                   step="any">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Capacity
                                            Width
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="vhcl_cpwd" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="vhcl_cpwd" value="{{$vehicle->vhcl_cpwd}}"
                                                   placeholder="Capacity Width" min="0" type="text"
                                                   step="any">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Capacity
                                            Length
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="vhcl_cplg" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="vhcl_cplg" value="{{$vehicle->vhcl_cplg}}"
                                                   placeholder="Capacity Length" min="0" type="text"
                                                   step="any">
                                        </div>
                                    </div>

                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button id="send" type="submit" class="btn btn-success">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            const vhcl_type = '{{ $vehicle->vhcl_type}}';
            const dpot_id = '{{ $vehicle->dpot_id }}';
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
    </script>
@endsection