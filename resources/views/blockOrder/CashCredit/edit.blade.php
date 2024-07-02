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
                            <a href="{{ URL::to('/cash_party_credit_budget')}}">All Cash Party Credit Budget</a>
                        </li>
                        <li class="active">
                            <strong>Edit Special Budget</strong>
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
                            <h3>Cash Party Credit Budget</h3>

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
                            <div class="col-md-1 col-sm-1 col-xs-12">

                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <table class="table table-striped projects">
                                <thead>
                                <tr>
                                    <th>S/L</th>
                                    <th> Amount</th>
                                    <th>Type</th>
                                    <th>TRN</th>
                                    <th>Time</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($line as $index => $ln)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$ln->scbd_amnt}}</td>
                                        <td>{{$ln->scbd_type}}</td>
                                        <td>{{$ln->ordm_ornm}}</td>
                                        <td>{{$ln->created_at}}</td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h3>Adjust Budget</h3>
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

                            <form class="form-horizontal form-label-left" action="{{route('cashCredit.update',$cashPartyCreditBudget->id)}}"
                                  method="post" enctype="multipart/form-data">
                                  {{csrf_field()}}
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Amount
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" class="form-control" name="amount" id="amount"
                                               value=""/>
                                    </div>
                                </div>

                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Type
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="trnt_id" id="trnt_id"
                                                required>
                                            <option value="1">In</option>
                                            <option value="2">Out</option>
                                        </select>
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

@endsection