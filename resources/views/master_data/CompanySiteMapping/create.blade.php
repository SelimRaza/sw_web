@extends('theme.app')
@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li >
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li >
                            <a href="{{ URL::to('/companySiteMapping')}}">All Company Site Mapping </a>
                        </li>
                        <li>
                            <strong>Company Site Mapping</strong>
                        </li>
                        <li class="label-success">
                            <a href="{{ URL::to('/companySiteMapping/uploadFormat')}}">Generate Upload
                                Format </a>
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
                        <strong>Success! </strong>{{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert-danger">
                        <strong>Alert! </strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12">
                    @if($permission->wsmu_updt)
                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <ul class="nav navbar-right panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                                                   role="button"
                                                   aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="#">Settings 1</a>
                                                    </li>
                                                    <li><a href="#">Settings 2</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                                            </li>
                                        </ul>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <br/>
                                        <form id="demo-form2" data-parsley-validate
                                              class="form-horizontal form-label-left"
                                              action="{{route('companySiteMapping.store')}}"
                                              method="post">
                                            {{csrf_field()}}
                                            {{method_field('post')}}
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Site Code<span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           name="site_code"
                                                           value="{{old('site_code')}}"
                                                           placeholder="Site Code" required="required" type="text">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                       for="first-name">Select Company
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control col-md-7 col-xs-12"
                                                            data-validate-length-range="6"
                                                            data-validate-words="2"
                                                            class="form-control" name="acmp_id" id="acmp_id"
                                                            required onchange="getGroup(this.value)">
                                                            <option value="">Select</option>
                                                        @foreach ($companies as $companies1)
                                                            <option value="{{ $companies1->id }}">{{ $companies1->acmp_name.' ('.$companies1->acmp_code.')' }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                       for="first-name">Select Group
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control col-md-7 col-xs-12"
                                                            data-validate-length-range="6"
                                                            data-validate-words="2"
                                                            class="form-control" name="slgp_id" id="slgp_id"
                                                            required>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                       for="first-name">Select Price List
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control col-md-7 col-xs-12"
                                                            data-validate-length-range="6"
                                                            data-validate-words="2"
                                                            class="form-control" name="plmt_id" id="plmt_id"
                                                            required>
                                                        @foreach ($priceList as $priceList1)
                                                            <option value="{{ $priceList1->id }}">{{ $priceList1->plmt_name.' ('.$priceList1->plmt_code.')' }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                       for="first-name">Select Credit Type
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control col-md-7 col-xs-12"
                                                            data-validate-length-range="6"
                                                            data-validate-words="2"
                                                            class="form-control" name="optp_id" id="optp_id"
                                                            required>
                                                        <option value="1">Cash</option>
                                                        <option value="2">Credit</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Credit Limit<span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           name="stcm_limt"
                                                           value="{{old('stcm_limt')}}"
                                                           placeholder="Limit" required="required" type="number">
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Credit Days<span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           name="stcm_days"
                                                           value="{{old('stcm_days')}}"
                                                           placeholder="Days" required="required" type="number">
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Credit Days Variable?
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="stcm_isfx" class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2" {{ old('stcm_isfx') == 'on' ? 'checked' : '' }}
                                                           name="stcm_isfx" type="checkbox"
                                                    >
                                                </div>
                                            </div>

                                            <div class="ln_solid"></div>
                                            <div class="form-group">
                                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                                    <button type="submit" class="btn btn-success">Save</button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($permission->wsmu_updt)
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
                                    <div class="x_title">

                                        <ul class="nav navbar-right panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                                                   role="button"
                                                   aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="#">Settings 1</a>
                                                    </li>
                                                    <li><a href="#">Settings 2</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                                            </li>
                                        </ul>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <br/>
                                        <form id="demo-form2" data-parsley-validate
                                              class="form-horizontal form-label-left"
                                              action="{{ URL::to('companySiteMapping/uploadInsert')}}"
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
    <script type="text/javascript">
        $(document).ready(function () {

            const acmp_id = '{{ old('acmp_id') }}';
            const plmt_id = '{{ old('plmt_id') }}';
            const optp_id = '{{ old('optp_id') }}';
            if (acmp_id !== '') {
                $('#acmp_id').val(acmp_id);
            }
            if (plmt_id !== '') {
                $('#plmt_id').val(plmt_id);
            }
            if (optp_id !== '') {
                $('#optp_id').val(optp_id);
            }
            $("#acmp_id").select2({width: 'resolve'});
            $("#plmt_id").select2({width: 'resolve'});
            $("#optp_id").select2({width: 'resolve'});
            $("#slgp_id").select2({width: 'resolve'});

        });
        function getGroup(slgp_id, place) {
                var _token = $("#_token").val();
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/load/report/getGroup",
                    data: {
                        slgp_id: slgp_id,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        $('#ajax_load').css("display", "none");
                        var html = '<option value="">Select</option>';
                        for (var i = 0; i < data.length; i++) {
                            console.log(data[i]);
                            html += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i].slgp_name + '</option>';
                        }
                        $('#slgp_id').empty();
                        $('#slgp_id').append(html);
                    
                    }
                });
            }
    </script>
@endsection