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
                            <a href="{{ URL::to('/cash_party_credit_budget')}}"> Cash Party Credit Budget</a>
                        </li>
                        <li class="active">
                            <strong>Credit Budget Details</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>
            <div class="clearfix"></div>

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
                            <h3>Credit Budget Details</h3>

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
                                @foreach($specialBudgetLine as $index => $specialBudgetLine1)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$specialBudgetLine1->scbd_amnt}}</td>
                                        <td>{{$specialBudgetLine1->scbd_type}}</td>
                                        <td>{{$specialBudgetLine1->ordm_ornm}}</td>
                                        <td>{{$specialBudgetLine1->created_at}}</td>

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
    </div>
@endsection