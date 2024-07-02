@extends('theme.app')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i> Home</a>
                        </li>
                        <li>
                            <a href="{{ URL::to('/self_account')}}"> All Account </a>
                        </li>
                        <li class="active">
                            <strong> Cash History </strong>
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
                    <strong>Danger! </strong>{{ Session::get('danger') }}
                </div>
            @endif
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>{{$selfAccount->name}}
                                <small>Cash History</small>
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
                                <?php
                                $tolal_amount = 0;
                                $credit_amount = 0;?>
                                @foreach($cashSources as $cashSource)
                                    <?php
                                    if ($cashSource->amount > 0) {
                                        $tolal_amount = $tolal_amount + $cashSource->amount;
                                    } else {
                                        $credit_amount = $credit_amount + $cashSource->amount;
                                    }

                                    ?>
                                    <tr>
                                        <td>{{$cashSource->id}}</td>
                                        <td>{{$cashSource->name}}</td>
                                        <td>{{$cashSource->amount}}</td>

                                    </tr>

                                @endforeach
                                <tr>
                                    <td colspan="2">Total Amount</td>

                                    <td>{{ $tolal_amount }}({{ $credit_amount*-1 }})</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>{{$selfAccount->name}}
                                <small>Cash History</small>
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
                            {{$cashMovements->appends(Request::only('start_date'))->appends(Request::only('end_date'))->appends(Request::only('move_type_id'))->links()}}
                            <form action="{{ URL::to('self_account/cash_history/'.$selfAccount->id)}}" method="get">
                                <div class="title_right">
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input type="text" class="form-control" name="start_date" id="start_date"
                                               value="<?php echo $start_date; ?>"/>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input type="text" class="form-control" name="end_date" id="end_date"
                                               value="<?php echo $end_date; ?>">
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12 form-group pull-right top_search">
                                        <div class="input-group">
                                            <select class="form-control" name="move_type_id" id="move_type_id">
                                                <option value="0"></option>
                                                @foreach($cashMoveTypes as $cashMoveType)
                                                    <option value='{{$cashMoveType->id}}'>{{ $cashMoveType->name }}</option>
                                                @endforeach


                                            </select>


                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12 form-group pull-right top_search">
                                        <div class="input-group">
                                            <select class="form-control" name="source_id" id="source_id">
                                                <option value="0"></option>
                                                @foreach($cashSources as $cashSource)
                                                    <option value='{{$cashSource->id}}'>{{ $cashSource->name }}</option>
                                                @endforeach


                                            </select>
                                            <span class="input-group-btn">
                      <button class="btn btn-default" type="submit">Go!</button>
                    </span>

                                        </div>
                                    </div>
                                </div>
                            </form>
                            <br/>
                            <table class="table table-striped projects">
                                <thead>

                                <tr>
                                    <th>SL</th>
                                    <th>Date</th>
                                    <th>Note</th>
                                    <th>Type</th>
                                    <th>Source</th>
                                    <th>In</th>
                                    <th>Out</th>
                                    <th>Sub Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $subTotal = 0;
                                $inTolal = 0;
                                $outTolal = 0 ?>
                                @foreach($cashMovements as  $index => $cashMovement)
                                    <?php
                                    $cashMovement->cash_type_id == 1 ? $inTolal = $inTolal + $cashMovement->amount : $outTolal = $outTolal + $cashMovement->amount;
                                    $cashMovement->cash_type_id == 1 ? $subTotal = $subTotal + $cashMovement->amount : $subTotal = $subTotal - $cashMovement->amount;
                                    ?>

                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$cashMovement->date}}</td>
                                        <td>{{$cashMovement->details}}</td>
                                        <td>{{$cashMovement->cashMoveType()->name}}</td>
                                        <td>{{$cashMovement->cashMoveSource()->name}}</td>
                                        <td>{{ $cashMovement->cash_type_id==1?$cashMovement->amount:0}}</td>
                                        <td>{{$cashMovement->cash_type_id==2?$cashMovement->amount:0}}</td>
                                        <td>{{$subTotal}}</td>
                                    </tr>

                                @endforeach
                                <tr>
                                    <td colspan="5">Total Amount</td>

                                    <td>{{ $inTolal}}</td>
                                    <td>{{$outTolal}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="6">Balance</td>


                                    <td> <?php echo $inTolal - $outTolal?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        document.getElementById('move_type_id').value = "<?php echo $move_type_id;?>";
        document.getElementById('source_id').value = "<?php echo $source_id;?>";
        $('#start_date').datetimepicker({format: 'YYYY-MM-DD'});
        $('#end_date').datetimepicker({format: 'YYYY-MM-DD'});
        $("#cash_move_type_id").select2({width: 'resolve'});

    </script>
@endsection