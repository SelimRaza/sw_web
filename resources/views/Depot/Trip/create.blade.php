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
                            <a href="{{ URL::to('/trip')}}">All Trip</a>
                        </li>
                        <li class="active">
                            <strong>New Trip</strong>
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
                        <strong>Danger! </strong>{{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert-danger">
                        <strong>Danger! </strong>{{ Session::get('danger') }}
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h1>Trip Create</h1>
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

                                <form class="form-horizontal form-label-left" action="{{route('trip.store')}}"
                                      method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Trip type
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="trip_type_id" id="trip_type_id"
                                                    required>
                                                <option value="">Select</option>
                                                @foreach ($dataTripType as $type)
                                                    <option value="{{ $type->id }}">{{ $type->ttyp_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Depot
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="depot_id" id="depot_id"
                                                    required>
                                                <option value="">Select</option>
                                                @foreach ($depots as $depot)
                                                    <option value="{{ serialize($depot) }}">{{ $depot->dpot_name.'-'.$depot->dlrm_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Emp Name
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="emp_id" id="emp_id"
                                                    required>
                                                <option value="">Select</option>
                                                @foreach ($dataEmp as $emp)
                                                    <option value="{{ $emp->id }}">{{ $emp->id.'-'.$emp->aemp_name.'('.$emp->aemp_usnm.')' }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Vehicle Name
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="vhcl_id" id="vhcl_id"
                                                    required>
                                                <option value="">Select</option>
                                                @foreach ($dataVhcl as $vhcl)
                                                    <option value="{{ $vhcl->id }}">{{ $vhcl->vhcl_name.'('.$vhcl->vhcl_code.')' }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Date
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="text" class="form-control" name="trip_date" id="trip_date"
                                                   value="<?php echo date('Y-m-d'); ?>"/>
                                        </div>
                                    </div>

                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button id="send" type="submit" class="btn btn-success">Next</button>
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
        $("#trip_type_id").select2({width: 'resolve'});
        $("#emp_id").select2({width: 'resolve'});
        $("#depot_id").select2({width: 'resolve'});
        $("#vhcl_id").select2({width: 'resolve'});
        $('#trip_date').datetimepicker({format: 'YYYY-MM-DD'});
    </script>
@endsection