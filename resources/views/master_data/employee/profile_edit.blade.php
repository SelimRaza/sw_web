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
                            <strong>Edit Profile</strong>
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
                            <h4><strong>Profile </strong></h4>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left col-md-6 col-sm-6 col-md-offset-1 col-sm-offset-1 col-xs-12"
                                  action="{{ URL::to('/employee/profileEdit/'.$employee->id)}}}}"
                                  method="post" enctype="multipart/form-data" >
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}
                                {{method_field('PUT')}}

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="item form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Operation <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <select class="form-control" name="country_id" id="country_id" required>
                                                    @foreach ($country as $country1)
                                                        <option value="{{ $country1->id }}">{{ ucfirst($country1->cont_name) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="item form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Full Name <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input id="name" class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2" name="name"
                                                       value="{{$employee->aemp_name}}"
                                                       placeholder="Name" required="required" type="text">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="item form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Ln Name
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input id="name" class="form-control col-md-7 col-xs-12"
                                                       name="ln_name"
                                                       value="{{$employee->aemp_onme}}"
                                                       placeholder="Ln Name" type="text">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="item form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Email
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input id="name" class="form-control col-md-7 col-xs-12"
                                                       name="address"
                                                       value="{{$employee->aemp_emal}}"
                                                       placeholder="Email" type="email">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="item form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Mobile
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input id="name" class="form-control col-md-7 col-xs-12"
                                                       name="mobile"
                                                       value="{{$employee->aemp_mob1}}"
                                                       placeholder="Mobile" type="text">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="item form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left"> Profile
                                                Image <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input id="name" name="input_img" type="file" capture>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <button id="send" type="submit" class="btn btn-primary btn-md"><span class="fa fa-check-circle" style="font-size: 1.3em"></span><b> Update</b></button>
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
        const cont_id = '{{$employee->cont_id }}';
        if (cont_id !== '') {
            $('#country_id').val(cont_id);
        }
        $("#country_id").select2({width: 'resolve'});
    </script>
@endsection
