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
                            <a href="{{ URL::to('/trade-marketing/zone')}}">Trade Marketing Zone List</a>
                        </li>
                        <li class="active">
                            <strong>Trade Marketing Zone</strong>
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

                {{-- Trade Marketing --}}
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div id="1a" class="x_content">
                                <form class="form-horizontal form-label-left" enctype="multipart/form-data">

                                    <div class="animate__animated animate__zoomIn">
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="aemp">Staff<span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input id="aemp"  class="form-control col-md-7 col-xs-12 in_tg"
                                                       placeholder="Staff ID" disabled
                                                       type="text" value="{{optional($trade->employee)->aemp_usnm}} - {{optional($trade->employee)->aemp_name}}">
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="zone">Zone<span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input id="zone"  class="form-control col-md-7 col-xs-12 in_tg"
                                                       placeholder="Staff ID" disabled
                                                       type="text" value="{{optional($trade->zone)->zone_code}} - {{optional($trade->zone)->zone_name}}">
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

    <script>
        $(document).ready(function (){
            $(".cmn_select2").select2();
        })
    </script>
@endsection