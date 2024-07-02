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
                            <a href="{{ URL::to('/self_account')}}">All Account </a>
                        </li>
                        <li class="active">
                            <strong>Cash Receive</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>
            @if(Session::has('success'))
                <div class="alert alert-success">
                    <strong>Success! </strong>{{ Session::get('success') }}
                </div>
            @endif
            @if(Session::has('danger'))
                <div class="alert alert-danger">
                    <strong>Warning! </strong>{{ Session::get('danger') }}
                </div>
            @endif
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>{{$selfAccount->name}}
                                <small>Cash Receive</small>
                            </h2>
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
                            <br/>
                            <table class="table table-striped projects">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($cashSources as $cashSource)
                                    <tr>
                                        <td>{{$cashSource->id}}</td>
                                        <td>{{$cashSource->name}}</td>
                                        <td>{{$cashSource->amount}}</td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @if($permission->wsmu_updt)
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>{{$selfAccount->name}}
                                    <small>Cash Receive</small>
                                </h2>
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
                                <br/>
                                <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left"
                                      action="{{ URL::to('self_account/cash_receive_add/'.$selfAccount->id)}}"
                                      method="GET">
                                    {{csrf_field()}}
                                    {{method_field('PUT')}}
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Date
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="text" class="form-control col-md-7 col-xs-12" name="date"
                                                   id="date"
                                                   value="<?php echo date('Y-m-d'); ?>"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Receive
                                            From
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="cash_move_type_id"
                                                    required>
                                                <option value="">Select</option>
                                                @foreach ($cashMoveTypes as $cashMoveType)
                                                    @if($cashMoveType->cash_type_id==1)
                                                        <option value="{{ $cashMoveType->id }}">{{ $cashMoveType->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Details
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="text" class="form-control col-md-7 col-xs-12" name="details"
                                                   id="details"
                                                   value=""/>

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Receive
                                            To
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="cash_source_id"
                                                    required>
                                                <option value="">Select</option>
                                                @foreach ($cashSources as $cashSource)
                                                    <option value="{{ $cashSource->id }}">{{ $cashSource->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Amount
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="number" step="any" class="form-control col-md-7 col-xs-12"
                                                   name="amount"
                                                   id="amount"
                                                   value=""/>
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
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>{{$selfAccount->name}}
                                <small>Cash Receive</small>
                            </h2>
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
                            <br/>
                            <table class="table table-striped projects">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Note</th>
                                    <th>Type</th>
                                    <th>Source</th>
                                    <th>Amount</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($cashMovements as $cashMovement)
                                    <tr>
                                        <td>{{$cashMovement->id}}</td>
                                        <td>{{$cashMovement->details}}</td>
                                        <td>{{$cashMovement->cashMoveType()->name}}</td>
                                        <td>{{$cashMovement->cashMoveSource()->name}}</td>
                                        <td>{{$cashMovement->amount}}</td>
                                        <td>
                                            @if($permission->wsmu_delt)
                                                <form style="display:inline"
                                                      action="{{ URL::to('self_account/cash_receive_delete/'.$cashMovement->id)}}"
                                                      class="pull-xs-right5 card-link" method="GET">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="<?php echo $cashMovement->status_id == 1 ? 'Inactive' : 'Active'?>"
                                                    >
                                                    </input>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#date').datetimepicker({format: 'YYYY-MM-DD'});
            $("select").select2({width: 'resolve'});
        });

        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Delete?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
@endsection