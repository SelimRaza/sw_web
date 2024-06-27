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
                            <a href="{{ URL::to('/no_delivery_reason')}}">All Not Delivery Reason</a>
                        </li>
                        <li class="active">
                            <strong>Edit Not Delivery Reason</strong>
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

                            <h4><strong>No Delivery Reason</strong></h4>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left"
                                  action="{{route('no_delivery_reason.update',$data->id)}}"
                                  method="post">
                                {{csrf_field()}}
                                {{method_field('PUT')}}
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Name <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input id="ondr_name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="ondr_name"
                                                   value="{{$data->ondr_name}}"
                                                   placeholder="Name" required="required" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="item form-group">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Code <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input id="ondr_code" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="ondr_code"
                                                   value="{{$data->ondr_code}}"
                                                   placeholder="Code" required="required" type="text">
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <button id="send" type="submit" class="btn btn-primary"><span
                                                        class="fa fa-check-circle"></span> Submit
                                            </button>
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
    </script>
@endsection