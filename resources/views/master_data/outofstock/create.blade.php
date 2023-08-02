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
                            <a href="{{ URL::to('/outofstock')}}">All OutOfStock</a>
                        </li>
                        <li class="active">
                            <strong>New OutOfStock</strong>
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
                            <h1 >OutOfStock</h1>
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

                            <form class="form-horizontal form-label-left" action="{{route('outofstock.store')}}"
                                  method="post">
                                {{csrf_field()}}


                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">DPO List<span
                                                class="required">*</span>
                                    </label>
                                   {{-- <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="dpot_id" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="dpot_id" value="{{old('dpot_id')}}"
                                               placeholder="Name" required="required" type="text">
                                    </div>--}}
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="dpot_id[]" id="dpot_id"
                                                placeholder="Select DPO" multiple required>
                                            <option value="">Select DPO</option>
                                            @foreach ($DPOList as $DPOList1)
                                                <option value="{{ $DPOList1->id }}">{{ ucfirst($DPOList1->DPO_Name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Item Code<span
                                                class="required">*</span>
                                    </label>
                                   {{-- <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="amim_id" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="amim_id" value="{{old('amim_id')}}"
                                               placeholder="Code" required="required" type="text">
                                    </div>--}}
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="amim_id" id="amim_id"
                                                placeholder="Select Item" required>
                                            <option value="">Select Item</option>
                                            @foreach ($ItemList as $ItemList1)
                                                <option value="{{ $ItemList1->id }}">{{ ucfirst($ItemList1->Item_Name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button id="send" type="submit" class="btn btn-success">Submit</button>
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
       // $("#dpot_id").select2();
       // $("#amim_id").select2();
        $("#dpot_id").select2({width: 'resolve'});
        $("#amim_id").select2({width: 'resolve'});
    </script>
@endsection