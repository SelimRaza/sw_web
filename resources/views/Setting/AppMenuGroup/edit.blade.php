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
                            <a href="{{ URL::to('/appMenuGroup')}}">All App Menu Profile</a>
                        </li>
                        <li class="active">
                            <strong>Edit App Menu Profile</strong>
                        </li>

                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        @if(Session::has('success'))
                            <div class="alert alert-success">
                                <strong></strong>{{ Session::get('success') }}
                            </div>
                        @endif
                        @if(Session::has('danger'))
                            <div class="alert alert-danger">
                                <strong></strong>{{ Session::get('danger') }}
                            </div>
                        @endif


                        <div class="x_title">
                            <h4><strong>App Menu Profile</strong></h4>
                            <div class="clearfix"></div>

                        </div>
                        <div class="x_content">

                            <table class="table table-striped projects">
                                <thead>
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border">SL</th>
                                    <th> Name</th>
                                    <th> Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($appMenuGroupLine as $index => $appMenuGroupLine1)
                                    <tr class="tbl_body_gray">
                                        <td class="cell_left_border">{{$index+1}}</td>
                                        <td>{{$appMenuGroupLine1->amnu_name}}</td>
                                        <td>@if($permission->wsmu_delt)

                                                <form style="display:inline"
                                                      action="{{ URL::to('appMenuGroup/menuDelete/'.$appMenuGroupLine1->amnd_id)}}"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                    {{method_field('POST')}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="Delete"
                                                    >
                                                    </input>
                                                </form>
                                            @endif</td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            @if($permission->wsmu_updt)
                <div class="row">

                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h4><strong>Profile Edit</strong></h4>
                                <div class="clearfix"></div>

                            </div>

                            <div class="x_content">

                                <form class="form-horizontal form-label-left"
                                      action="{{route('appMenuGroup.update',$appMenuGroup->id)}}"
                                      enctype="multipart/form-data"
                                      method="post">
                                    {{csrf_field()}}
                                    {{method_field('PUT')}}


                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Name <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" name="amng_name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   placeholder="Name" required="required" type="text"
                                                   value="{{$appMenuGroup->amng_name}}">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Code <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="code" name="amng_code" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   placeholder="Code" required="required" type="text"
                                                   value="{{$appMenuGroup->amng_code}}">
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
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h4><strong>App Menu Profile Add</strong></h4>
                                <div class="clearfix"></div>

                            </div>

                            <div class="x_content">

                                <form class="form-horizontal form-label-left"
                                      action="{{ URL::to('appMenuGroup/assignMenu/'.$appMenuGroup->id)}}"
                                      method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    {{method_field('post')}}


                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Menu name
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="amnu_id[]" id="amnu_id" multiple
                                                    required>
                                                <option value="">Select</option>
                                                @foreach ($mobileMenu as $mobileMenu1)
                                                    <option value="{{ $mobileMenu1->id }}">{{ $mobileMenu1->id.'-'.$mobileMenu1->amnu_name.'('.$mobileMenu1->amnu_code.')' }}</option>
                                                @endforeach
                                            </select>
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
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h4><strong>Add To User List {{$appMenuGroup->amng_name}}</strong></h4>
                                <div class="clearfix"></div>

                            </div>

                            <div class="x_content">
                                <br/>
                                <form id="demo-form2" data-parsley-validate
                                      class="form-horizontal form-label-left"
                                      action="{{ URL::to('appMenuGroup/assignToUser/'.$appMenuGroup->id)}}"
                                      enctype="multipart/form-data"
                                      method="post">
                                    {{csrf_field()}}
                                    {{method_field('POST')}}
                                    <div class="form-group">

                                        <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 10px;">
                                            <a class="btn btn-danger" href="{{ URL::to('/appMenuGroup/uploadFormat/'.$appMenuGroup->id)}}"> Upload Format </a>
                                        </div>
                                        <br />
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
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button type="submit" class="btn btn-primary"><i class="fa fa-check-circle"></i> Upload
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
    <script>
        $(document).ready(function () {
            $("#amnu_id").select2({width: 'resolve'});
        });
    </script>


@endsection