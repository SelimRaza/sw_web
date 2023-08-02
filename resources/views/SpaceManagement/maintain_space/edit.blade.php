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
                            <a href="{{ URL::to('/maintain/space')}}">Space List</a>
                        </li>
                        <li class="active">
                            <strong>Edit Space</strong>
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

                @if($errors->any())
                     <div class="alert alert-danger" style="font-family:sans-serif;">
                         <p><strong>Opps Something went wrong</strong></p>
                         <ol>
                         @foreach ($errors->all() as $error)
                             <li>{{ $error}}</li>
                         @endforeach
                         </ol>
                     </div>
                 @endif
                    
                {{-- Space Maintain --}}
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
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
                                <div id="exTab1" class="container">
                                    <ul class="nav nav-pills">
                                        <li>
                                            <a href="#1a" data-toggle="tab" selected>Edit Space</a>
                                        </li>
                                    </ul>

                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div id="1a" class="x_content">
                                <form class="form-horizontal form-label-left spcm" action="{{URL::to('maintain/space/'.$space->id)}}"
                                      method="post" enctype="multipart/form-data" id="spcm">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    @method('PUT')
                                   
                                        <div class="animate__animated animate__zoomIn">
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="dfdm_name">Space
                                                    Name
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="spcm_name" name="spcm_name" class="form-control col-md-7 col-xs-12 in_tg"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           placeholder="Space  Name"
                                                           type="text" value="{{ $space->spcm_name }}">
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Space
                                                    Code
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="spcm_code" name="spcm_code" class="form-control col-md-7 col-xs-12 in_tg"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           placeholder=" Space Code" readonly
                                                           type="text" value="{{ $space->spcm_code }}">
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="start_date">Start
                                                    Date
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="start_date" name="spcm_sdat" readonly
                                                           class="form-control col-md-7 col-xs-12 in_tg"
                                                           value="{{ $space->spcm_sdat }}" required="required" type="text">
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">End
                                                    Date
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="end_date" name="spcm_edat"
                                                           class="form-control col-md-7 col-xs-12 in_tg date"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           value="{{ $space->spcm_exdt }}" type="text">
                                                </div>
                                            </div>
{{--                                            <div class="item form-group">--}}
{{--                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name"> Qualifier--}}
{{--                                                </label>--}}
{{--                                                <div class="col-md-6 col-sm-6 col-xs-12">--}}
{{--                                                    <input name="spcm_qyfr" readonly--}}
{{--                                                           class="form-control col-md-7 col-xs-12"--}}
{{--                                                           value="{{ $space->spcm_qyfr==1 ? 'Value' : 'FOC' }}" type="text">--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
                                           <div class="item form-group">
                                                <div class="col-md-9 col-sm-9">
                                                   <button type="submit" class="btn btn-primary" style="float:right;">Save</button>
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
        </div>
        <style>
            .fa-times-circle-o:hover{
                cursor: pointer;
            }

            #radio:hover{
                color: #73879C;
                cursor: pointer;
            }
        </style>
    <script>

        $(document).ready(function (){


            $('.date').datepicker({
                dateFormat: 'yy-mm-dd',
                autoclose: 1,
                showOnFocus: true
            });

        })
    </script>
    @endsection