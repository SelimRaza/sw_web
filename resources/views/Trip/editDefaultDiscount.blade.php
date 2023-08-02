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
                            <a href="{{ URL::to('/default-discount')}}">Default Discount List</a>
                        </li>
                        <li class="active">
                            <strong>Edit Default Discount</strong>
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
                                
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <form class="form-horizontal form-label-left" action="{{route('dfdm.update',$dfdm->id)}}"
                                      method="post" enctype="multipart/form-data" id="dfdsc">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                   
                                        <div class="animate__animated animate__zoomIn">
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="dfdm_name">Discount
                                                    Name <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="dfdm_name" name="dfdm_name" class="form-control col-md-7 col-xs-12 in_tg"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           placeholder="Discount  Name" required
                                                           type="text" value="{{$dfdm->dfdm_name}}">
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Discount
                                                    Code <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="dfdm_code" name="dfdm_code" class="form-control col-md-7 col-xs-12 in_tg"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           placeholder=" Discount Code" required="required"
                                                           type="text" value="{{$dfdm->dfdm_code}}" disabled>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="start_date">Start
                                                    Date <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="start_date" name="start_date"
                                                           class="form-control col-md-7 col-xs-12 in_tg"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           value="{{$dfdm->start_date}}" type="text" disabled>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">End
                                                    Date <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="end_date" name="end_date"
                                                           class="form-control col-md-7 col-xs-12 in_tg"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           value="{{$dfdm->end_date}}" required="required" type="text">
                                                </div>
                                            </div>
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
<script>
    $('#start_date').datetimepicker({format: 'YYYY-MM-DD'});
    $('#end_date').datetimepicker({format: 'YYYY-MM-DD'});
    $("#end_date").datepicker({
        dateFormat: 'yy-mm-dd',
        minDate: '0d',
        changeMonth: true
    });
</script>
@endsection