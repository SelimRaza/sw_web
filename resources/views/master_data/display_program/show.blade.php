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
                            <a href="{{ URL::to('/display-program')}}">All Display Program</a>
                        </li>
                        <li class="active">
                            <strong>Show Display Program</strong>
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
                            <h1>Display Program</h1>
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

                                  method="post">
                                {{csrf_field()}}

                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="name"
                                               value="{{$displayProgram->name}}"
                                               placeholder="Name" required="required" type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Code <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" readonly class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="code"
                                               value="{{$displayProgram->code}}"
                                               placeholder="Code" required="required" type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Maximum <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="name" readonly class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="max_width"
                                               placeholder="Width" value="{{$displayProgram->max_width}}"
                                               required="required" type="text">
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="name" readonly class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="max_height"
                                               placeholder="Height" value="{{$displayProgram->max_height}}"
                                               required="required" type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Minimum <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="name" readonly class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="min_width"
                                               placeholder="Width" value="{{$displayProgram->min_width}}"
                                               required="required" type="text">
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="name" readonly class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="min_height"
                                               placeholder="Height" value="{{$displayProgram->min_height}}"
                                               required="required" type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Date Range <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input type="text" readonly class="form-control" name="start_date" id="start_date"
                                               value="{{$displayProgram->start_date}}"/>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input type="text" readonly class="form-control" name="end_date" id="end_date"
                                               value="{{$displayProgram->end_date}}">
                                    </div>
                                </div>
                                <table class="table table-striped projects">
                                    <thead>
                                    <tr>
                                        <th>
                                            Display SKU
                                        </th>
                                        <th>
                                            Qty
                                        </th>
                                        <th>
                                            Amount
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($displayProgramConditions as $displayProgramCondition)
                                        @if($displayProgramCondition->type==1)
                                            <tr>
                                                <td>{{$displayProgramCondition->sku()->name.'('.$displayProgramCondition->sku()->code.')'}}</td>
                                                <td>{{$displayProgramCondition->qty}}</td>
                                                <td>{{$displayProgramCondition->amount}}</td>

                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                                <table class="table table-striped projects">
                                    <thead>
                                    <tr>
                                        <th>
                                            Condition SKU
                                        </th>
                                        <th>
                                            Qty
                                        </th>
                                        <th>
                                            Amount
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($displayProgramConditions as $displayProgramCondition)
                                        @if($displayProgramCondition->type==2)
                                            <tr>
                                                <td>{{$displayProgramCondition->sku()->name.'('.$displayProgramCondition->sku()->code.')'}}</td>
                                                <td>{{$displayProgramCondition->qty}}</td>
                                                <td>{{$displayProgramCondition->amount}}</td>

                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                                <table class="table table-striped projects">
                                    <thead>
                                    <tr>
                                        <th>
                                            Offer SKU
                                        </th>
                                        <th>
                                            Qty
                                        </th>
                                        <th>
                                            Amount
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($displayProgramConditions as $displayProgramCondition)
                                        @if($displayProgramCondition->type==3)
                                            <tr>
                                                <td>{{$displayProgramCondition->sku()->name.'('.$displayProgramCondition->sku()->code.')'}}</td>
                                                <td>{{$displayProgramCondition->qty}}</td>
                                                <td>{{$displayProgramCondition->amount}}</td>

                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection