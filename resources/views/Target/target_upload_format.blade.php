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
                            <a href="{{ URL::to('/target/upload')}}"> Target Upload</a>
                        </li>
                        <li class="active">
                            <strong>Generate Target Format</strong>
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
                        <strong>Error! </strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Generate Target Format</h1>
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

                            <form class="form-horizontal form-label-left"
                                  action="{{URL::to('/target/uploadFormatGen')}}"
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="col-md-4">
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="usr">Manager Id:</label>
                                                <input id="user_name" class="form-control col-md-7 col-xs-6"
                                                       data-validate-length-range="6"
                                                       data-validate-words="2" name="manager"
                                                       value="{{old('manager')}}"
                                                       placeholder="user_name" required="required"
                                                       type="text">
                                            </div>
                                            <div class="form-group">
                                                <label for="usr">Month:</label>
                                                <input type="text" class="form-control" name="trgt_date"
                                                       id="spbm_date"
                                                       value="<?php echo date('Y-m'); ?>"/>
                                            </div>
                                            <div class="form-group">
                                                <label for="usr">Price List:</label>
                                                <select class="control-label col-md-3 col-sm-3 col-xs-3"
                                                        data-validate-length-range="6"
                                                        data-validate-words="2"
                                                        class="form-control" name="price_list" id="price_list"
                                                        required>
                                                    <option value="">Select</option>
                                                    @foreach ($priceLists as $priceList)
                                                        <option value="{{ $priceList->id }}">{{ $priceList->name.' ('.$priceList->code.')' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="ln_solid"></div>
                                            <div class="form-group">
                                                <div class="col-md-6 col-md-offset-3">
                                                    <button id="send" type="submit" class="btn btn-success">Submit</button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-4">
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
        $("#price_list").select2({width: 'resolve'});
        $('#spbm_date').datetimepicker({format: 'YYYY-MM'});
    </script>
@endsection